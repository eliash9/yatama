<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\BankMutation;
use App\Models\Income;
use App\Models\Account;
use Illuminate\Http\Request;

class BankMutationPageController extends Controller
{
    public function index()
    {
        $rows = BankMutation::with(['income'])->orderByDesc('tanggal')->paginate(15)->withQueryString();
        $incomeCandidates = \App\Models\Income::where('status','recorded')->orderByDesc('tanggal')->limit(20)->get(['id','receipt_no','amount','tanggal']);
        $trxCandidates = \App\Models\Transaksi::orderByDesc('tanggal')->limit(20)->get(['id','tanggal','amount','jenis']);
        return view('finance.mutations.index', compact('rows','incomeCandidates','trxCandidates'));
    }

    public function import(Request $request)
    {
        $data = $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);
        $csv = fopen($request->file('file')->getRealPath(), 'r');
        $count = 0;
        while (($row = fgetcsv($csv)) !== false) {
            // Expect columns: tanggal(YYYY-MM-DD), amount, description, channel, ref_no
            if (count($row) < 2) continue;
            $tgl = $row[0] ?? null; $amount = (int)($row[1] ?? 0);
            $desc = $row[2] ?? null; $channel = $row[3] ?? null; $ref = $row[4] ?? null;
            if (!$tgl || !$amount) continue;
            BankMutation::create([
                'tanggal' => $tgl,
                'amount' => $amount,
                'description' => $desc,
                'channel' => $channel,
                'ref_no' => $ref,
            ]);
            $count++;
        }
        fclose($csv);
        return back()->with('status', "Import $count mutasi selesai");
    }

    public function automatch()
    {
        $matched = 0;
        $mutations = BankMutation::whereNull('matched_income_id')->get();
        foreach ($mutations as $m) {
            $candidate = null;
            if (!empty($m->ref_no)) {
                $candidate = Income::where('status','recorded')->where('ref_no',$m->ref_no)->first();
            }
            if (!$candidate) {
                $start = date('Y-m-d', strtotime($m->tanggal.' -2 days'));
                $end   = date('Y-m-d', strtotime($m->tanggal.' +2 days'));
                $q = Income::where('status','recorded')
                    ->where('amount', $m->amount)
                    ->whereBetween('tanggal', [$start,$end]);
                if (!empty($m->channel)) { $q->where('channel',$m->channel); }
                $candidate = $q->orderBy('tanggal')->first();
            }
            if ($candidate) {
                $m->matched_income_id = $candidate->id; $m->save();
                $candidate->status = 'matched'; $candidate->save();
                $this->autoJournalIncome($m, $candidate);
                $matched++;
            }
        }
        return back()->with('status', "Auto-match berhasil: $matched baris");
    }

    public function match(Request $request, BankMutation $mutation)
    {
        $data = $request->validate([
            'income_id' => 'nullable|exists:incomes,id',
            'receipt_no' => 'nullable|string',
            'transaction_id' => 'nullable|exists:transaksi,id'
        ]);
        // Prioritas: match ke transaksi jika diisi
        if (empty($data['transaction_id'])) {
            if (!$mutation->matched_income_id) {
                $income = null;
                if (!empty($data['income_id'])) {
                    $income = \App\Models\Income::where('status','recorded')->find($data['income_id']);
                } elseif (!empty($data['receipt_no'])) {
                    $income = \App\Models\Income::where('status','recorded')->where('receipt_no',$data['receipt_no'])->first();
                }
                if (!$income) {
                    return back()->withErrors(['income_id' => 'Income tidak ditemukan atau sudah matched']);
                }
                $mutation->matched_income_id = $income->id; $mutation->save();
                $income->status = 'matched'; $income->save();
                $this->autoJournalIncome($mutation, $income);
                return back()->with('status','Mutasi -> Income berhasil dihubungkan');
            }
            return back()->withErrors(['mutation' => 'Mutasi sudah terhubung ke income']);
        } else {
            if (!$mutation->matched_transaction_id) {
                $trx = \App\Models\Transaksi::find($data['transaction_id']);
                if (!$trx) return back()->withErrors(['transaction_id' => 'Transaksi tidak ditemukan']);
                $mutation->matched_transaction_id = $trx->id; $mutation->save();
                $trx->reconciled_at = now(); $trx->save();
                return back()->with('status','Mutasi -> Transaksi berhasil dihubungkan');
            }
            return back()->withErrors(['mutation' => 'Mutasi sudah terhubung ke transaksi']);
        }
    }

    private function autoJournalIncome(BankMutation $m, Income $income): void
    {
        $existing = \App\Models\Transaksi::where('ref_type','income')->where('ref_id',$income->id)->first();
        if ($existing) {
            if (!$m->matched_transaction_id) { $m->matched_transaction_id = $existing->id; $m->save(); }
            return;
        }
        $acc = $this->pickAccountForChannel($m->channel);
        $trx = \App\Models\Transaksi::create([
            'tanggal' => $m->tanggal,
            'jenis' => 'debit',
            'akun_kas' => $acc?->code ?? 'BANK',
            'account_id' => $acc?->id,
            'amount' => $m->amount,
            'ref_type' => 'income',
            'ref_id' => $income->id,
            'program_id' => $income->program_id,
            'memo' => 'Auto journal '.$income->receipt_no.' via '.($m->channel ?: 'bank'),
            'reconciled_at' => now(),
        ]);
        $m->matched_transaction_id = $trx->id; $m->save();
    }

    private function pickAccountForChannel(?string $channel): ?Account
    {
        $map = [
            'qris' => env('MUTATION_ACCOUNT_QRIS_CODE'),
            'va' => env('MUTATION_ACCOUNT_VA_CODE'),
            'transfer' => env('MUTATION_ACCOUNT_TRANSFER_CODE'),
        ];
        $c = strtolower((string)$channel);
        $code = $map[$c] ?? env('MUTATION_ACCOUNT_DEFAULT_CODE');
        if ($code) {
            $acc = Account::where('code',$code)->where('is_active',true)->first();
            if ($acc) return $acc;
        }
        $acc = Account::where('is_active',true)->where('code','like','1.1.2.%')->orderBy('id')->first();
        if ($acc) return $acc;
        return Account::where('is_active',true)->orderBy('id')->first();
    }
}

