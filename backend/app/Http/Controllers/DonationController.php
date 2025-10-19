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
            'channel' => 'required|in:transfer,qris,ewallet',
            'provider' => 'nullable|string|max:50',
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

        // Combine user note with meta for payment instructions
        $userNote = trim($data['notes'] ?? '');
        $meta = [
            'channel' => $data['channel'],
            'provider' => $data['provider'] ?? null,
        ];
        $notesCombined = ($userNote ? ($userNote.' ') : '') . '[meta='.json_encode($meta).']';

        $income = Income::create([
            'receipt_no' => $receipt,
            'tanggal' => date('Y-m-d'),
            'channel' => $data['channel'],
            'amount' => $data['amount'],
            'donor_id' => $donorId,
            'program_id' => $data['program_id'] ?? null,
            'status' => 'recorded',
            'ref_no' => $ref,
            'notes' => $notesCombined ?: null,
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

        // Parse provider from notes meta suffix
        $pay = ['channel' => $row->channel, 'provider' => null];
        if ($row->notes && preg_match('/\[meta=(\{.*\})\]/', $row->notes, $m)) {
            try { $meta = json_decode($m[1], true); if (is_array($meta)) { $pay = array_merge($pay, $meta); } } catch (\Throwable $e) {}
        }

        // Bank and ewallet accounts from env
        $banks = [
            'BCA' => ['account' => env('DONATION_BANK_BCA'), 'name' => env('DONATION_BANK_BCA_NAME')],
            'BNI' => ['account' => env('DONATION_BANK_BNI'), 'name' => env('DONATION_BANK_BNI_NAME')],
            'MANDIRI' => ['account' => env('DONATION_BANK_MANDIRI'), 'name' => env('DONATION_BANK_MANDIRI_NAME')],
        ];
        $ewallets = [
            'OVO' => ['number' => env('DONATION_EWALLET_OVO'), 'name' => env('DONATION_EWALLET_OVO_NAME')],
            'GOPAY' => ['number' => env('DONATION_EWALLET_GOPAY'), 'name' => env('DONATION_EWALLET_GOPAY_NAME')],
            'DANA' => ['number' => env('DONATION_EWALLET_DANA'), 'name' => env('DONATION_EWALLET_DANA_NAME')],
        ];

        return view('public.donation.status', compact('row','qrisUrl','pay','banks','ewallets'));
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
