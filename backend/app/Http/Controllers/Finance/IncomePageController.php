<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Donor;
use App\Models\Program;
use App\Models\Lampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IncomePageController extends Controller
{
    public function index(Request $request)
    {
        $q = Income::query()->with(['donor','program']);
        if ($c = $request->query('channel')) $q->where('channel',$c);
        if ($s = $request->query('search')) {
            $like = "%$s%";
            $q->where(function($qq) use ($like){
                $qq->where('receipt_no','like',$like)->orWhere('ref_no','like',$like);
            });
        }
        $rows = $q->orderByDesc('tanggal')->paginate(10)->withQueryString();
        return view('finance.incomes.index', compact('rows'));
    }

    public function create()
    {
        $row = new Income(['tanggal'=>date('Y-m-d')]);
        $donors = Donor::orderBy('name')->get(['id','name']);
        $programs = Program::orderBy('name')->get(['id','name']);
        return view('finance.incomes.form', compact('row','donors','programs'));
    }

    private function generateReceiptNo(): string
    {
        $prefix = 'KW-'.date('Ymd').'-';
        $last = Income::where('receipt_no','like',$prefix.'%')->orderByDesc('id')->value('receipt_no');
        $seq = 1;
        if ($last && preg_match('/-(\d{4})$/',$last,$m)) { $seq = intval($m[1]) + 1; }
        return $prefix.str_pad((string)$seq,4,'0',STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'channel' => 'required|in:transfer,qris,va,tunai,gateway',
            'amount' => 'required|integer|min:0',
            'donor_id' => 'nullable|exists:donors,id',
            'program_id' => 'nullable|exists:programs,id',
            'ref_no' => 'nullable|string',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|max:5120',
        ]);
        $data['receipt_no'] = $this->generateReceiptNo();
        $income = Income::create($data);

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts','public');
            Lampiran::create([
                'ref_type' => 'income',
                'ref_id' => $income->id,
                'filename' => $request->file('receipt')->getClientOriginalName(),
                'mime' => $request->file('receipt')->getClientMimeType(),
                'size' => $request->file('receipt')->getSize(),
                'url' => 'storage/'.$path,
                'uploader_id' => $request->user()->id,
            ]);
        }

        return redirect()->route('finance.incomes.index')->with('status','Penerimaan dicatat');
    }

    public function edit(Income $income)
    {
        $row = $income;
        $donors = Donor::orderBy('name')->get(['id','name']);
        $programs = Program::orderBy('name')->get(['id','name']);
        return view('finance.incomes.form', compact('row','donors','programs'));
    }

    public function show(Income $income)
    {
        $row = $income->load(['donor','program']);
        $attachments = \App\Models\Lampiran::where('ref_type','income')->where('ref_id',$row->id)->orderByDesc('uploaded_at')->get();
        return view('finance.incomes.show', compact('row','attachments'));
    }

    public function receipt(Income $income)
    {
        $row = $income->load(['donor','program']);
        return view('finance.incomes.receipt', compact('row'));
    }

    public function update(Request $request, Income $income)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'channel' => 'required|in:transfer,qris,va,tunai,gateway',
            'amount' => 'required|integer|min:0',
            'donor_id' => 'nullable|exists:donors,id',
            'program_id' => 'nullable|exists:programs,id',
            'ref_no' => 'nullable|string',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|max:5120',
        ]);
        $income->update($data);
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts','public');
            Lampiran::create([
                'ref_type' => 'income',
                'ref_id' => $income->id,
                'filename' => $request->file('receipt')->getClientOriginalName(),
                'mime' => $request->file('receipt')->getClientMimeType(),
                'size' => $request->file('receipt')->getSize(),
                'url' => 'storage/'.$path,
                'uploader_id' => $request->user()->id,
            ]);
        }
        return redirect()->route('finance.incomes.index')->with('status','Penerimaan diubah');
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return redirect()->route('finance.incomes.index')->with('status','Penerimaan dihapus');
    }
}
