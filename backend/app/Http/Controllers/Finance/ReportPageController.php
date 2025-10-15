<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Program;
use App\Models\Transaksi;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportPageController extends Controller
{
    public function balances(Request $request)
    {
        // Saldo per Akun
        $accounts = Account::orderBy('name')->get();
        $accountBalances = [];
        foreach ($accounts as $a) {
            $net = Transaksi::where('account_id',$a->id)
                ->select(DB::raw("COALESCE(SUM(CASE WHEN jenis='debit' THEN amount ELSE -amount END),0) as net"))
                ->value('net') ?? 0;
            $accountBalances[] = [
                'account' => $a,
                'balance' => (int)$a->opening_balance + (int)$net,
            ];
        }

        // Saldo per Program (masuk - keluar)
        $incomes = Income::select('program_id', DB::raw('SUM(amount) as total'))
            ->groupBy('program_id')->pluck('total','program_id');
        $spends = Transaksi::whereNotNull('program_id')
            ->select('program_id', DB::raw("SUM(CASE WHEN jenis='debit' THEN amount ELSE -amount END) as total"))
            ->groupBy('program_id')->pluck('total','program_id');
        $programs = Program::orderBy('name')->get(['id','name']);
        $programBalances = [];
        foreach ($programs as $p) {
            $in = (int) ($incomes[$p->id] ?? 0);
            $out = (int) ($spends[$p->id] ?? 0);
            $programBalances[] = [ 'program' => $p, 'balance' => $in + $out ]; // out is signed (debit - kredit)
        }

        return view('finance.reports.balances', compact('accountBalances','programBalances'));
    }

    public function incomes(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $q = \App\Models\Income::query();
        if ($from) $q->where('tanggal','>=',$from); if ($to) $q->where('tanggal','<=',$to);
        $byChannel = $q->clone()->select('channel', DB::raw('SUM(amount) as total'))->groupBy('channel')->pluck('total','channel');
        $byProgram = $q->clone()->select(DB::raw('COALESCE(program_id,0) as pid'), DB::raw('SUM(amount) as total'))->groupBy('pid')->orderByDesc('total')->get();
        $programNames = \App\Models\Program::whereIn('id', $byProgram->pluck('pid')->filter())->pluck('name','id');
        return view('finance.reports.incomes', compact('from','to','byChannel','byProgram','programNames'));
    }

    public function incomesCsv(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $rows = \App\Models\Income::with(['donor','program'])
            ->when($from, fn($q)=>$q->where('tanggal','>=',$from))
            ->when($to, fn($q)=>$q->where('tanggal','<=',$to))
            ->orderByDesc('tanggal')->get();
        $filename = 'incomes_'.date('Ymd_His').'.csv';
        return response()->streamDownload(function() use ($rows){
            $out = fopen('php://output','w');
            fputcsv($out, ['Tanggal','No Kwitansi','Kanal','Donatur','Program','Jumlah','Status','Ref']);
            foreach ($rows as $r) {
                fputcsv($out, [$r->tanggal,$r->receipt_no,$r->channel,$r->donor->name ?? '',$r->program->name ?? 'General Fund',$r->amount,$r->status,$r->ref_no]);
            }
            fclose($out);
        }, $filename, ['Content-Type'=>'text/csv']);
    }

    public function disbursements(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $q = \App\Models\Disbursement::query()->with('beneficiary');
        if ($from) $q->whereDate('created_at','>=',$from); if ($to) $q->whereDate('created_at','<=',$to);
        $rows = $q->orderByDesc('id')->paginate(20)->withQueryString();
        $byBeneficiary = \App\Models\Disbursement::select('beneficiary_id', DB::raw('SUM(amount) as total'))
            ->when($from, fn($qq)=>$qq->whereDate('created_at','>=',$from))
            ->when($to, fn($qq)=>$qq->whereDate('created_at','<=',$to))
            ->groupBy('beneficiary_id')->orderByDesc('total')->limit(10)->get();
        $byRegion = \App\Models\Disbursement::join('beneficiaries','beneficiaries.id','=','disbursements.beneficiary_id')
            ->select('beneficiaries.region', DB::raw('SUM(disbursements.amount) as total'))
            ->when($from, fn($qq)=>$qq->whereDate('disbursements.created_at','>=',$from))
            ->when($to, fn($qq)=>$qq->whereDate('disbursements.created_at','<=',$to))
            ->groupBy('beneficiaries.region')->orderByDesc('total')->get();
        return view('finance.reports.disbursements', compact('from','to','rows','byBeneficiary','byRegion'));
    }

    public function disbursementsCsv(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $rows = \App\Models\Disbursement::with(['program','beneficiary'])
            ->when($from, fn($q)=>$q->whereDate('created_at','>=',$from))
            ->when($to, fn($q)=>$q->whereDate('created_at','<=',$to))
            ->orderByDesc('id')->get();
        $filename = 'disbursements_'.date('Ymd_His').'.csv';
        return response()->streamDownload(function() use ($rows){
            $out = fopen('php://output','w');
            fputcsv($out, ['Kode','Program','Penerima','Jumlah','Status','Tanggal']);
            foreach ($rows as $r) {
                fputcsv($out, [$r->code,$r->program->name ?? '',$r->beneficiary->name ?? '',$r->amount,$r->status,optional($r->created_at)->format('Y-m-d')]);
            }
            fclose($out);
        }, $filename, ['Content-Type'=>'text/csv']);
    }

    public function cashflow(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $q = \App\Models\Transaksi::query();
        if ($from) $q->where('tanggal','>=',$from); if ($to) $q->where('tanggal','<=',$to);
        $series = $q->select(DB::raw("DATE_FORMAT(tanggal,'%Y-%m') as ym"), DB::raw("SUM(CASE WHEN jenis='debit' THEN amount ELSE 0 END) as debit"), DB::raw("SUM(CASE WHEN jenis='kredit' THEN amount ELSE 0 END) as kredit"))
            ->groupBy('ym')->orderBy('ym')->get();
        return view('finance.reports.cashflow', compact('from','to','series'));
    }

    public function cashflowCsv(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $q = \App\Models\Transaksi::query();
        if ($from) $q->where('tanggal','>=',$from); if ($to) $q->where('tanggal','<=',$to);
        $series = $q->select(DB::raw("DATE_FORMAT(tanggal,'%Y-%m') as ym"), DB::raw("SUM(CASE WHEN jenis='debit' THEN amount ELSE 0 END) as debit"), DB::raw("SUM(CASE WHEN jenis='kredit' THEN amount ELSE 0 END) as kredit"))
            ->groupBy('ym')->orderBy('ym')->get();
        $filename = 'cashflow_'.date('Ymd_His').'.csv';
        return response()->streamDownload(function() use ($series){
            $out = fopen('php://output','w');
            fputcsv($out, ['Periode','Debit','Kredit']);
            foreach ($series as $r) { fputcsv($out, [$r->ym,$r->debit,$r->kredit]); }
            fclose($out);
        }, $filename, ['Content-Type'=>'text/csv']);
    }

    public function funds(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $inc = \App\Models\Income::query(); if ($from) $inc->where('tanggal','>=',$from); if ($to) $inc->where('tanggal','<=',$to);
        $earmark = (int) $inc->clone()->whereNotNull('program_id')->sum('amount');
        $general = (int) $inc->clone()->whereNull('program_id')->sum('amount');
        $spendProgram = (int) \App\Models\Transaksi::whereNotNull('program_id')
            ->when($from, fn($q)=>$q->where('tanggal','>=',$from))
            ->when($to, fn($q)=>$q->where('tanggal','<=',$to))
            ->where('jenis','kredit')->sum('amount');
        $spendGeneral = (int) \App\Models\Transaksi::whereNull('program_id')
            ->when($from, fn($q)=>$q->where('tanggal','>=',$from))
            ->when($to, fn($q)=>$q->where('tanggal','<=',$to))
            ->where('jenis','kredit')->sum('amount');
        return view('finance.reports.funds', [
            'from'=>$from,'to'=>$to,
            'earmark_in'=>$earmark, 'general_in'=>$general,
            'earmark_out'=>$spendProgram, 'general_out'=>$spendGeneral,
        ]);
    }

    public function campaigns(Request $request)
    {
        $programs = \App\Models\Program::orderBy('name')->get();
        $totals = \App\Models\Income::select('program_id', DB::raw('SUM(amount) as total'))->whereNotNull('program_id')->groupBy('program_id')->pluck('total','program_id');
        return view('finance.reports.campaigns', compact('programs','totals'));
    }

    public function operationalRatio(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $ops = \App\Models\Transaksi::when($from, fn($q)=>$q->where('tanggal','>=',$from))
            ->when($to, fn($q)=>$q->where('tanggal','<=',$to))
            ->where('jenis','kredit')->where('category','operational')->sum('amount');
        $programSpend = \App\Models\Transaksi::when($from, fn($q)=>$q->where('tanggal','>=',$from))
            ->when($to, fn($q)=>$q->where('tanggal','<=',$to))
            ->where('jenis','kredit')->whereNotNull('program_id')->sum('amount');
        return view('finance.reports.operational_ratio', compact('from','to','ops','programSpend'));
    }
}
