<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Disbursement;
use App\Models\DisbursementApproval;
use App\Models\DisbursementPayment;
use App\Models\Program;
use App\Models\Beneficiary;
use App\Models\Account;
use App\Models\Lampiran;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DisbursementPageController extends Controller
{
    public function index(Request $request)
    {
        $q = Disbursement::query()->with(['program','beneficiary','requester']);
        if ($request->filled('status')) $q->where('status',$request->status);
        if ($request->filled('program_id')) $q->where('program_id',$request->program_id);
        $rows = $q->orderByDesc('id')->paginate(15)->withQueryString();
        $programs = Program::orderBy('name')->get(['id','name']);
        return view('finance.disbursements.index', compact('rows','programs'));
    }

    public function create()
    {
        $row = new Disbursement();
        $programs = Program::orderBy('name')->get(['id','name']);
        $beneficiaries = Beneficiary::orderBy('name')->get(['id','name']);
        return view('finance.disbursements.form', compact('row','programs','beneficiaries'));
    }

    private function generateCode(): string
    {
        $prefix = 'DSB-'.date('Y').'-';
        $last = Disbursement::where('code','like',$prefix.'%')->orderByDesc('id')->value('code');
        $seq = 1; if ($last && preg_match('/-(\\d{4})$/',$last,$m)) $seq = intval($m[1]) + 1;
        return $prefix.str_pad((string)$seq,4,'0',STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'amount' => 'required|integer|min:1',
            'method_preference' => 'nullable|in:cash,transfer,ewallet',
            'purpose' => 'nullable|string',
        ]);
        $data['code'] = $this->generateCode();
        $data['requested_by'] = $request->user()->id;
        $data['status'] = 'draft';
        $row = Disbursement::create($data);
        return redirect()->route('finance.disbursements.show', $row)->with('status','Pengajuan dibuat');
    }

    public function show(Disbursement $disbursement)
    {
        $row = $disbursement->load(['program','beneficiary','approvals','payments']);
        $attachments = Lampiran::where('ref_type','disbursement')->where('ref_id',$row->id)->orderByDesc('uploaded_at')->get();
        $accounts = Account::where('is_active',true)->orderBy('name')->get(['id','name','code']);
        return view('finance.disbursements.show', compact('row','attachments','accounts'));
    }

    public function edit(Disbursement $disbursement)
    {
        $row = $disbursement;
        if (!in_array($row->status,['draft','submitted'])) abort(403);
        $programs = Program::orderBy('name')->get(['id','name']);
        $beneficiaries = Beneficiary::orderBy('name')->get(['id','name']);
        return view('finance.disbursements.form', compact('row','programs','beneficiaries'));
    }

    public function update(Request $request, Disbursement $disbursement)
    {
        if (!in_array($disbursement->status,['draft','submitted'])) abort(403);
        $data = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'amount' => 'required|integer|min:1',
            'method_preference' => 'nullable|in:cash,transfer,ewallet',
            'purpose' => 'nullable|string',
        ]);
        $disbursement->update($data);
        return redirect()->route('finance.disbursements.show',$disbursement)->with('status','Pengajuan diubah');
    }

    public function submit(Disbursement $disbursement)
    {
        if ($disbursement->status !== 'draft') abort(403);
        $disbursement->update(['status'=>'submitted','submitted_at'=>now()]);
        return back()->with('status','Pengajuan dikirim');
    }

    public function assess(Request $request, Disbursement $disbursement)
    {
        if (!in_array($disbursement->status,['submitted','assessed'])) abort(403);
        $data = $request->validate(['assessed_note'=>'nullable|string']);
        $disbursement->update([
            'assessed_by'=>$request->user()->id,
            'assessed_note'=>$data['assessed_note'] ?? null,
            'assessed_at'=>now(),
            'status'=>'assessed',
        ]);
        return back()->with('status','Asesmen tersimpan');
    }

    public function verifyProgram(Request $request, Disbursement $disbursement)
    {
        if (!in_array($disbursement->status,['assessed','program_verified'])) abort(403);
        DisbursementApproval::updateOrCreate(
            ['disbursement_id'=>$disbursement->id,'level'=>1],
            ['approver_id'=>$request->user()->id,'status'=>'approved','decided_at'=>now()]
        );
        $disbursement->update(['status'=>'program_verified']);
        return back()->with('status','Verifikasi Program OK');
    }

    public function verifyFinance(Request $request, Disbursement $disbursement)
    {
        if (!in_array($disbursement->status,['program_verified','finance_verified'])) abort(403);
        DisbursementApproval::updateOrCreate(
            ['disbursement_id'=>$disbursement->id,'level'=>2],
            ['approver_id'=>$request->user()->id,'status'=>'approved','decided_at'=>now()]
        );
        $disbursement->update(['status'=>'finance_verified']);
        return back()->with('status','Verifikasi Keuangan OK');
    }

    public function approve(Request $request, Disbursement $disbursement)
    {
        if (!in_array($disbursement->status,['finance_verified','approved'])) abort(403);
        DisbursementApproval::updateOrCreate(
            ['disbursement_id'=>$disbursement->id,'level'=>3],
            ['approver_id'=>$request->user()->id,'status'=>'approved','decided_at'=>now()]
        );
        $disbursement->update(['status'=>'approved']);
        return back()->with('status','Disetujui akhir');
    }

    public function pay(Request $request, Disbursement $disbursement)
    {
        if (!in_array($disbursement->status,['approved','paid'])) abort(403);
        $data = $request->validate([
            'channel' => 'required|in:cash,transfer,ewallet',
            'account_id' => 'nullable|exists:accounts,id',
            'amount' => 'required|integer|min:1',
            'recipient_name' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_no' => 'nullable|string',
            'ewallet_id' => 'nullable|string',
            'ref_no' => 'nullable|string',
            'receipt' => 'nullable|file|max:5120',
        ]);

        $payment = null;
        DB::transaction(function () use ($request,$disbursement,$data,&$payment) {
            $payment = DisbursementPayment::create([
                'disbursement_id'=>$disbursement->id,
                'channel'=>$data['channel'],
                'account_id'=>$data['account_id'] ?? null,
                'amount'=>$data['amount'],
                'paid_at'=>now(),
                'recipient_name'=>$data['recipient_name'] ?? null,
                'bank_name'=>$data['bank_name'] ?? null,
                'account_no'=>$data['account_no'] ?? null,
                'ewallet_id'=>$data['ewallet_id'] ?? null,
                'ref_no'=>$data['ref_no'] ?? null,
                'created_by'=>$request->user()->id,
            ]);

            if ($request->hasFile('receipt')) {
                $path = $request->file('receipt')->store('disb-receipts','public');
                $url = 'storage/'.$path;
                $payment->update(['receipt_url'=>$url]);
                Lampiran::create([
                    'ref_type'=>'disbursement','ref_id'=>$disbursement->id,
                    'filename'=>$request->file('receipt')->getClientOriginalName(),
                    'mime'=>$request->file('receipt')->getClientMimeType(),
                    'size'=>$request->file('receipt')->getSize(),
                    'url'=>$url,'uploader_id'=>$request->user()->id,
                ]);
            }

            // catat transaksi keluar
            $akun = $payment->account_id ? Account::find($payment->account_id) : null;
            Transaksi::create([
                'tanggal'=>date('Y-m-d'),
                'jenis'=>'kredit',
                'akun_kas'=>$akun?->code ?? ($data['channel']==='cash'?'CASH':'BANK'),
                'account_id'=>$akun?->id,
                'amount'=>$payment->amount,
                'ref_type'=>'disbursement','ref_id'=>$disbursement->id,
                'program_id'=>$disbursement->program_id,
                'memo'=>'Disbursement '.$disbursement->code.' to '.$payment->recipient_name,
            ]);

            $disbursement->update(['status'=>'paid']);
        });

        return back()->with('status','Pembayaran berhasil dieksekusi');
    }
}

