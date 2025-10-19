<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Donor;

class DonorAuthController extends Controller
{
    public function showLogin()
    {
        return view('public.donor.login');
    }

    public function requestCode(Request $request)
    {
        $data = $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
        ]);

        $email = isset($data['email']) ? strtolower(trim($data['email'])) : null;
        $phone = isset($data['phone']) ? $this->normalizePhone($data['phone']) : null;

        if (!$email && !$phone) {
            return back()->withErrors(['email' => 'Isi email atau nomor HP']);
        }

        $lookup = $email ? ['email' => $email] : ['phone' => $phone];
        $donor = Donor::firstOrCreate($lookup, [
            'code' => 'PUB-'.strtoupper(Str::random(6)),
            'type' => 'individual',
            'name' => 'Donatur',
            'is_active' => true,
        ]);

        $token = strtoupper(Str::random(6));
        $donor->update(['login_token'=>$token, 'login_token_expires_at'=>now()->addMinutes(15)]);

        // DEV: log token; in prod send via email/SMS gateway
        Log::info('Donor login token', [
            'email'=>$donor->email,
            'phone'=>$donor->phone,
            'token'=>$token,
        ]);

        // Store identity in session for verify prefill
        session([
            'donor_login_type' => $email ? 'email' : 'phone',
            'donor_login_value' => $email ?: $phone,
        ]);

        return redirect()->route('public.donation.account.verify')->with('status','Kode login telah dikirim (cek log aplikasi saat dev).');
    }

    public function showVerify()
    {
        return view('public.donor.verify', [
            'prefillType' => session('donor_login_type'),
            'prefillValue' => session('donor_login_value'),
        ]);
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'token' => 'required|string',
        ]);

        $sessionType = session('donor_login_type');
        $sessionValue = session('donor_login_value');
        $email = isset($data['email']) ? strtolower(trim($data['email'])) : null;
        $phone = isset($data['phone']) ? $this->normalizePhone($data['phone']) : null;

        if ($sessionType && $sessionValue) {
            if ($sessionType === 'email') { $email = strtolower(trim($sessionValue)); $phone = null; }
            if ($sessionType === 'phone') { $phone = $this->normalizePhone($sessionValue); $email = null; }
        }

        if (!$email && !$phone) {
            return back()->withErrors(['token'=>'Isi email atau nomor HP']);
        }

        $token = strtoupper(trim($data['token']));
        $donor = $email
            ? Donor::whereRaw('LOWER(email) = ?', [$email])->first()
            : Donor::where('phone', $phone)->first();

        if (!$donor || !$donor->login_token || !($donor->login_token_expires_at && now()->lte($donor->login_token_expires_at)) || strtoupper($donor->login_token) !== $token) {
            \Log::warning('Donor token verify failed', [
                'email_input'=>$email,
                'phone_input'=>$phone,
                'has_donor'=> (bool) $donor,
                'has_token'=> $donor?->login_token ? true : false,
                'expires_at'=> $donor?->login_token_expires_at,
                'now'=> now(),
            ]);
            return back()->withErrors(['token'=>'Token tidak valid atau kadaluarsa']);
        }

        session(['donor_id'=>$donor->id]);
        // Invalidate token and cleanup prefill after use
        $donor->update(['login_token'=>null,'login_token_expires_at'=>null]);
        session()->forget(['donor_login_type','donor_login_value']);

        return redirect()->route('public.donation.account.dashboard');
    }

    public function redirectToGoogle()
    {
        return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            \Log::error('Google auth failed', ['e'=>$e->getMessage()]);
            return redirect()->route('public.donation.account.login')->withErrors(['login'=>'Gagal masuk dengan Google']);
        }

        $email = strtolower($googleUser->getEmail());
        $name = $googleUser->getName() ?: 'Donatur';

        $donor = Donor::firstOrCreate(['email'=>$email], [
            'code' => 'PUB-'.strtoupper(Str::random(6)),
            'type' => 'individual',
            'name' => $name,
            'is_active' => true,
        ]);

        if (!$donor->name || $donor->name === 'Donatur') {
            $donor->update(['name'=>$name]);
        }

        session(['donor_id'=>$donor->id]);
        session()->forget(['donor_login_type','donor_login_value']);
        return redirect()->route('public.donation.account.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['donor_id','donor_login_type','donor_login_value']);
        return redirect()->route('public.donation.account.login');
    }

    private function normalizePhone(string $raw): string
    {
        $digits = preg_replace('/[^0-9]/', '', $raw);
        // Simple normalization: leading 0 -> 62 (Indonesia), else keep as is
        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits,1);
        }
        return $digits;
    }
}


