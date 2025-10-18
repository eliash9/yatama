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
        $data = $request->validate(['email' => 'required|email']);
        $email = strtolower(trim($data['email']));
        $donor = Donor::firstOrCreate(['email'=>$email], [
            'code' => 'PUB-'.strtoupper(Str::random(6)),
            'type' => 'individual',
            'name' => 'Donatur',
            'is_active' => true,
        ]);
        $token = strtoupper(Str::random(6));
        $donor->update(['login_token'=>$token, 'login_token_expires_at'=>now()->addMinutes(15)]);
        Log::info('Donor login token', ['email'=>$donor->email, 'token'=>$token]);
        return redirect()->route('public.donor.verify')->with('status','Kode login telah dikirim (cek log aplikasi saat dev).');
    }

    public function showVerify()
    {
        return view('public.donor.verify');
    }

    public function verify(Request $request)
    {
        $data = $request->validate(['email'=>'required|email','token'=>'required|string']);
        $email = strtolower(trim($data['email']));
        $token = strtoupper(trim($data['token']));
        $donor = Donor::whereRaw('LOWER(email) = ?', [$email])->first();
        if (!$donor || !$donor->login_token || !($donor->login_token_expires_at && now()->lte($donor->login_token_expires_at)) || strtoupper($donor->login_token) !== $token) {
            \Log::warning('Donor token verify failed', [
                'email_input'=>$email,
                'has_donor'=> (bool) $donor,
                'has_token'=> $donor?->login_token ? true : false,
                'expires_at'=> $donor?->login_token_expires_at,
                'now'=> now(),
            ]);
            return back()->withErrors(['token'=>'Token tidak valid atau kadaluarsa']);
        }
        session(['donor_id'=>$donor->id]);
        // Invalidate token after use
        $donor->update(['login_token'=>null,'login_token_expires_at'=>null]);
        return redirect()->route('public.donor.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('donor_id');
        return redirect()->route('public.donor.login');
    }
}
