<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Donor;
use App\Models\Program;
use App\Models\Lampiran;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class IncomePageController extends Controller
{
    use IncomeCashJournalHelper;
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

        // Ringkasan kecil: per status, per kanal, per program (top 5)
        $byStatus = (clone $q)
            ->select('status', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(amount) as total'))
            ->groupBy('status')->get();
        $byChannel = (clone $q)
            ->select('channel', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(amount) as total'))
            ->groupBy('channel')->get();
        $byProgram = (clone $q)
            ->select('program_id', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(amount) as total'))
            ->groupBy('program_id')
            ->orderBy(DB::raw('SUM(amount)'), 'DESC')->limit(5)->get();
        $programNames = Program::whereIn('id', $byProgram->pluck('program_id')->filter())
            ->pluck('name','id');

        return view('finance.incomes.index', compact('rows','byStatus','byChannel','byProgram','programNames'));
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
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        // Generate unique receipt_no with simple retry to avoid race condition
        for ($i=0; $i<3; $i++) {
            try {
                $data['receipt_no'] = $this->generateReceiptNo();
                $income = Income::create($data);
                break;
            } catch (\Illuminate\Database\QueryException $e) {
                if ($i === 2) { throw $e; }
                usleep(50000); // 50ms before retry
            }
        }

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

        // Auto-journal untuk kanal tunai (langsung masuk buku kas)
        if ($income->channel === 'tunai') {
            $this->autoJournalCashIncome($income);
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
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
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
        // Sinkronisasi jurnal untuk kanal tunai (buat jika belum ada, atau perbarui nilai/tanggal)
        if ($income->channel === 'tunai') {
            $this->autoJournalCashIncome($income, true);
        }

        return redirect()->route('finance.incomes.index')->with('status','Penerimaan diubah');
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return redirect()->route('finance.incomes.index')->with('status','Penerimaan dihapus');
    }
}

// Helper untuk pencatatan kas tunai
namespace App\Http\Controllers\Finance;

trait IncomeCashJournalHelper
{
    private function autoJournalCashIncome(\App\Models\Income $income, bool $allowUpdate = false): void
    {
        $existing = \App\Models\Transaksi::where('ref_type','income')->where('ref_id',$income->id)->first();
        if ($existing) {
            if ($allowUpdate) {
                $existing->update([
                    'tanggal' => $income->tanggal,
                    'amount' => $income->amount,
                    'program_id' => $income->program_id,
                    'reconciled_at' => $existing->reconciled_at ?: now(),
                ]);
            }
            return;
        }
        $acc = $this->pickCashAccount();
        \App\Models\Transaksi::create([
            'tanggal' => $income->tanggal,
            'jenis' => 'debit',
            'akun_kas' => $acc?->code ?? 'CASH',
            'account_id' => $acc?->id,
            'amount' => $income->amount,
            'ref_type' => 'income',
            'ref_id' => $income->id,
            'program_id' => $income->program_id,
            'memo' => 'Penerimaan tunai '.$income->receipt_no,
            'reconciled_at' => now(),
        ]);
    }

    private function pickCashAccount(): ?\App\Models\Account
    {
        $code = env('INCOME_CASH_ACCOUNT_CODE', '1.1.1'); // Kas Tunai default
        $acc = \App\Models\Account::where('code',$code)->where('is_active',true)->first();
        if ($acc) return $acc;
        $acc = \App\Models\Account::where('is_active',true)->where('type','cash')->orderBy('id')->first();
        if ($acc) return $acc;
        return \App\Models\Account::where('is_active',true)->orderBy('id')->first();
    }
}
