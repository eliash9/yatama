<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Income;
use App\Models\Donor;
use App\Models\Program;
use App\Models\Disbursement;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $programs = Program::orderBy('name')->get(['id','name','target_amount','banner_url','description']);
        // Terkumpul per program
        $collected = \App\Models\Income::select('program_id', \Illuminate\Support\Facades\DB::raw('SUM(amount) as total'))
            ->whereNotNull('program_id')->groupBy('program_id')->pluck('total','program_id');
        // Donatur terbaru
        $recentDonors = \App\Models\Income::with('donor')
            ->whereNotNull('donor_id')->orderByDesc('id')->limit(10)->get(['id','donor_id','amount','tanggal']);
        // Berita penyaluran (paid)
        $recentDisb = Disbursement::with(['program','beneficiary'])
            ->where('status','paid')->orderByDesc('id')->limit(5)->get(['id','code','program_id','beneficiary_id','amount','updated_at']);
        $qrisUrl = env('DONATION_QRIS_URL');
        return view('public.donation.index', compact('programs','collected','recentDonors','recentDisb','qrisUrl'));
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:120',
            'phone' => 'nullable|string|max:30',
            'program_id' => 'nullable|exists:programs,id',
            'amount' => 'required|integer|min:10000',
            'channel' => 'required|in:transfer,qris',
            'notes' => 'nullable|string|max:500',
        ]);

        // Prefer session donor if logged-in, else create/find based on email/phone
        $donorId = session('donor_id');
        if (!$donorId && (!empty($data['email']) || !empty($data['phone']) || !empty($data['name']))) {
            $email = isset($data['email']) ? strtolower(trim($data['email'])) : null;
            $phone = isset($data['phone']) ? trim($data['phone']) : null;
            $donor = Donor::firstOrCreate(
                ['email' => $email, 'phone' => $phone],
                [
                    'code' => 'PUB-'.strtoupper(Str::random(6)),
                    'type' => 'individual',
                    'name' => $data['name'] ?? 'Donatur',
                    'is_active' => true,
                ]
            );
            $donorId = $donor->id;
        }

        $receipt = $this->generateReceiptNo();
        $ref = strtoupper(Str::uuid()->toString());

        $income = Income::create([
            'receipt_no' => $receipt,
            'tanggal' => date('Y-m-d'),
            'channel' => $data['channel'],
            'amount' => $data['amount'],
            'donor_id' => $donorId,
            'program_id' => $data['program_id'] ?? null,
            'status' => 'recorded',
            'ref_no' => $ref,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('public.donation.status', ['ref' => $income->ref_no]);
    }

    public function status(Request $request)
    {
        $ref = $request->query('ref');
        if (!$ref) {
            return view('public.donation.status_lookup');
        }
        $row = Income::where('ref_no',$ref)->first();
        if (!$row) {
            return back()->withErrors(['ref'=>'Ref tidak ditemukan']);
        }
        $qrisUrl = env('DONATION_QRIS_URL');
        return view('public.donation.status', compact('row','qrisUrl'));
    }

    public function thanks(Request $request)
    {
        $ref = $request->query('ref');
        $row = $ref ? Income::where('ref_no',$ref)->first() : null;
        if (!$row) abort(404);
        return view('public.donation.thanks', compact('row'));
    }

    private function generateReceiptNo(): string
    {
        $prefix = 'KW-'.date('Ymd').'-';
        do {
            $seq = strtoupper(Str::random(6));
            $code = $prefix.$seq;
        } while (Income::where('receipt_no',$code)->exists());
        return $code;
    }

    public function program(Request $request, Program $program)
    {
        $qrisUrl = env('DONATION_QRIS_URL');
        $t = (int) ($program->target_amount ?? 0);
        $collected = \App\Models\Income::where('program_id',$program->id)->sum('amount');
        $pct = $t > 0 ? min(100, intval($collected*100/$t)) : null;
        $recentDonors = \App\Models\Income::with('donor')
            ->where('program_id',$program->id)->whereNotNull('donor_id')
            ->orderByDesc('id')->limit(10)->get(['id','donor_id','amount','tanggal']);
        return view('public.donation.program', compact('program','qrisUrl','t','collected','pct','recentDonors'));
    }
}
