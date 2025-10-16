<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Program;
use App\Models\Transaksi;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\IncomesExport;
use App\Exports\DisbursementsExport;
use App\Exports\CashflowExport;
use App\Exports\BalancesExport;
use App\Exports\FundsExport;
use App\Exports\CampaignsExport;
use App\Exports\OperationalRatioExport;

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

    public function balancesExcel(Request $request)
    {
        return new BalancesExport();
    }

    public function balancesPdf(Request $request)
    {
        // Reuse logic from balances()
        $accounts = \App\Models\Account::orderBy('name')->get();
        $accountBalances = [];
        foreach ($accounts as $a) {
            $net = \App\Models\Transaksi::where('account_id',$a->id)
                ->select(DB::raw("COALESCE(SUM(CASE WHEN jenis='debit' THEN amount ELSE -amount END),0) as net"))
                ->value('net') ?? 0;
            $accountBalances[] = [ 'account' => $a, 'balance' => (int)$a->opening_balance + (int)$net ];
        }
        $incomes = \App\Models\Income::select('program_id', DB::raw('SUM(amount) as total'))->groupBy('program_id')->pluck('total','program_id');
        $spends = \App\Models\Transaksi::whereNotNull('program_id')
            ->select('program_id', DB::raw("SUM(CASE WHEN jenis='debit' THEN amount ELSE -amount END) as total"))
            ->groupBy('program_id')->pluck('total','program_id');
        $programs = \App\Models\Program::orderBy('name')->get(['id','name']);
        $programBalances = [];
        foreach ($programs as $p) {
            $in = (int) ($incomes[$p->id] ?? 0); $out = (int) ($spends[$p->id] ?? 0);
            $programBalances[] = [ 'program' => $p, 'balance' => $in + $out ];
        }
        $pdf = Pdf::loadView('exports.balances_pdf', compact('accountBalances','programBalances'));
        return $pdf->download('balances_'.date('Ymd_His').'.pdf');
    }

    public function incomes(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $q = \App\Models\Income::query();
        if ($from) $q->where('tanggal','>=',$from); if ($to) $q->where('tanggal','<=',$to);
        $q1 = clone $q; $q2 = clone $q;
        $byChannel = $q1->select('channel', DB::raw('SUM(amount) as total'))->groupBy('channel')->pluck('total','channel');
        $byProgram = $q2->select(DB::raw('COALESCE(program_id,0) as pid'), DB::raw('SUM(amount) as total'))->groupBy('pid')->orderByDesc('total')->get();
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

    public function incomesExcel(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        return new IncomesExport($from, $to);
    }

    public function incomesPdf(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $rows = \App\Models\Income::with(['donor','program'])
            ->when($from, fn($q)=>$q->where('tanggal','>=',$from))
            ->when($to, fn($q)=>$q->where('tanggal','<=',$to))
            ->orderByDesc('tanggal')->get();
        $pdf = Pdf::loadView('exports.incomes_pdf', compact('rows','from','to'));
        return $pdf->download('incomes_'.date('Ymd_His').'.pdf');
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

    public function disbursementsExcel(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        return new DisbursementsExport($from, $to);
    }

    public function disbursementsPdf(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $rows = \App\Models\Disbursement::with(['program','beneficiary'])
            ->when($from, fn($q)=>$q->whereDate('created_at','>=',$from))
            ->when($to, fn($q)=>$q->whereDate('created_at','<=',$to))
            ->orderByDesc('id')->get();
        $pdf = Pdf::loadView('exports.disbursements_pdf', compact('rows','from','to'));
        return $pdf->download('disbursements_'.date('Ymd_His').'.pdf');
    }

    public function cashflow(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $series = $this->cashflowSeries($from, $to);
        return view('finance.reports.cashflow', compact('from','to','series'));
    }

    public function cashflowCsv(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $series = $this->cashflowSeries($from, $to);
        $filename = 'cashflow_'.date('Ymd_His').'.csv';
        return response()->streamDownload(function() use ($series){
            $out = fopen('php://output','w');
            fputcsv($out, ['Periode','Debit','Kredit']);
            foreach ($series as $r) { fputcsv($out, [$r->ym,$r->debit,$r->kredit]); }
            fclose($out);
        }, $filename, ['Content-Type'=>'text/csv']);
    }

    public function cashflowExcel(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        return new CashflowExport($from, $to);
    }

    public function cashflowPdf(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $series = $this->cashflowSeries($from, $to);
        $pdf = Pdf::loadView('exports.cashflow_pdf', compact('series','from','to'));
        return $pdf->download('cashflow_'.date('Ymd_His').'.pdf');
    }

    protected function cashflowSeries(?string $from, ?string $to)
    {
        $driver = DB::connection()->getDriverName();
        $q = \App\Models\Transaksi::query();
        if ($from) $q->where('tanggal','>=',$from); if ($to) $q->where('tanggal','<=',$to);
        try {
            if ($driver === 'mysql' || $driver === 'mariadb') {
                return $q->select(DB::raw("DATE_FORMAT(tanggal,'%Y-%m') as ym"), DB::raw("SUM(CASE WHEN jenis='debit' THEN amount ELSE 0 END) as debit"), DB::raw("SUM(CASE WHEN jenis='kredit' THEN amount ELSE 0 END) as kredit"))
                    ->groupBy('ym')->orderBy('ym')->get();
            } elseif ($driver === 'sqlite') {
                return $q->select(DB::raw("strftime('%Y-%m', tanggal) as ym"), DB::raw("SUM(CASE WHEN jenis='debit' THEN amount ELSE 0 END) as debit"), DB::raw("SUM(CASE WHEN jenis='kredit' THEN amount ELSE 0 END) as kredit"))
                    ->groupBy('ym')->orderBy('ym')->get();
            } elseif ($driver === 'pgsql') {
                return $q->select(DB::raw("to_char(tanggal, 'YYYY-MM') as ym"), DB::raw("SUM(CASE WHEN jenis='debit' THEN amount ELSE 0 END) as debit"), DB::raw("SUM(CASE WHEN jenis='kredit' THEN amount ELSE 0 END) as kredit"))
                    ->groupBy('ym')->orderBy('ym')->get();
            }
        } catch (\Throwable $e) {
            // Fallback to PHP grouping below
        }
        $rows = $q->get(['tanggal','jenis','amount']);
        $map = [];
        foreach ($rows as $row) {
            $ym = substr((string)$row->tanggal, 0, 7);
            if (!isset($map[$ym])) $map[$ym] = ['ym'=>$ym,'debit'=>0,'kredit'=>0];
            if ($row->jenis === 'debit') $map[$ym]['debit'] += (int)$row->amount; else $map[$ym]['kredit'] += (int)$row->amount;
        }
        ksort($map);
        return collect(array_values(array_map(function($v){ return (object)$v; }, $map)));
    }

    public function funds(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $inc = \App\Models\Income::query(); if ($from) $inc->where('tanggal','>=',$from); if ($to) $inc->where('tanggal','<=',$to);
        $inc1 = clone $inc; $inc2 = clone $inc;
        $earmark = (int) $inc1->whereNotNull('program_id')->sum('amount');
        $general = (int) $inc2->whereNull('program_id')->sum('amount');
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

    public function fundsExcel(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        return new FundsExport($from, $to);
    }

    public function fundsPdf(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $inc = \App\Models\Income::query(); if ($from) $inc->where('tanggal','>=',$from); if ($to) $inc->where('tanggal','<=',$to);
        $earmark = (int) (clone $inc)->whereNotNull('program_id')->sum('amount');
        $general = (int) (clone $inc)->whereNull('program_id')->sum('amount');
        $spendProgram = (int) \App\Models\Transaksi::whereNotNull('program_id')
            ->when($from, fn($q)=>$q->where('tanggal','>=',$from))
            ->when($to, fn($q)=>$q->where('tanggal','<=',$to))
            ->where('jenis','kredit')->sum('amount');
        $spendGeneral = (int) \App\Models\Transaksi::whereNull('program_id')
            ->when($from, fn($q)=>$q->where('tanggal','>=',$from))
            ->when($to, fn($q)=>$q->where('tanggal','<=',$to))
            ->where('jenis','kredit')->sum('amount');
        $pdf = Pdf::loadView('exports.funds_pdf', compact('from','to','earmark','general','spendProgram','spendGeneral'));
        return $pdf->download('funds_'.date('Ymd_His').'.pdf');
    }

    public function campaigns(Request $request)
    {
        $programs = \App\Models\Program::orderBy('name')->get();
        $totals = \App\Models\Income::select('program_id', DB::raw('SUM(amount) as total'))->whereNotNull('program_id')->groupBy('program_id')->pluck('total','program_id');
        return view('finance.reports.campaigns', compact('programs','totals'));
    }

    public function campaignsExcel(Request $request)
    {
        return new CampaignsExport();
    }

    public function campaignsPdf(Request $request)
    {
        $programs = \App\Models\Program::orderBy('name')->get();
        $totals = \App\Models\Income::select('program_id', DB::raw('SUM(amount) as total'))->whereNotNull('program_id')->groupBy('program_id')->pluck('total','program_id');
        $pdf = Pdf::loadView('exports.campaigns_pdf', compact('programs','totals'));
        return $pdf->download('campaigns_'.date('Ymd_His').'.pdf');
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

    public function operationalRatioExcel(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        return new OperationalRatioExport($from, $to);
    }

    public function operationalRatioPdf(Request $request)
    {
        $from = $request->query('from'); $to = $request->query('to');
        $ops = \App\Models\Transaksi::when($from, fn($q)=>$q->where('tanggal','>=',$from))
            ->when($to, fn($q)=>$q->where('tanggal','<=',$to))
            ->where('jenis','kredit')->where('category','operational')->sum('amount');
        $programSpend = \App\Models\Transaksi::when($from, fn($q)=>$q->where('tanggal','>=',$from))
            ->when($to, fn($q)=>$q->where('tanggal','<=',$to))
            ->where('jenis','kredit')->whereNotNull('program_id')->sum('amount');
        $pdf = Pdf::loadView('exports.operational_ratio_pdf', compact('from','to','ops','programSpend'));
        return $pdf->download('operational_ratio_'.date('Ymd_His').'.pdf');
    }
}
