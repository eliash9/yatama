<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\Pengajuan;
use App\Models\Transaksi;
use App\Models\Unit;
use App\Models\Periode;
use App\Models\Disbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardPageController extends Controller
{
    public function __invoke(Request $request)
    {
        $unitId = $request->query('unit_id');
        $periodeId = $request->query('periode_id');

        $pengajuanQuery = Pengajuan::query();
        if ($unitId) $pengajuanQuery->where('unit_id', $unitId);
        if ($periodeId) $pengajuanQuery->where('periode_id', $periodeId);

        $countsRaw = (clone $pengajuanQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->pluck('count','status')->toArray();
        $labels = ['draft','diajukan','ditinjau','disetujui','ditolak','dicairkan','selesai'];
        $counts = array_fill_keys($labels, 0);
        foreach ($countsRaw as $k=>$v) { if (isset($counts[$k])) $counts[$k] = (int)$v; }
        if (array_sum($counts) === 0) {
            // Fallback ke distribusi Disbursement jika tidak ada pengajuan
            $dq = Disbursement::query();
            if ($unitId) $dq->whereHas('program', fn($q)=>$q->where('unit_id',$unitId));
            $disb = $dq->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')->pluck('count','status');
            $map = [
                'draft'=>'draft','submitted'=>'diajukan','assessed'=>'ditinjau',
                'program_verified'=>'disetujui','finance_verified'=>'disetujui',
                'approved'=>'disetujui','paid'=>'dicairkan','rejected'=>'ditolak',
            ];
            foreach ($disb as $st=>$cnt) { $to = $map[$st] ?? null; if ($to) $counts[$to] += (int)$cnt; }
        }

        $totalDimintaApproved = (int) (clone $pengajuanQuery)
            ->whereIn('status', ['disetujui','dicairkan','selesai'])
            ->sum('total_diminta');

        $anggaranQuery = Anggaran::query();
        if ($unitId) $anggaranQuery->where('unit_id', $unitId);
        if ($periodeId) $anggaranQuery->where('periode_id', $periodeId);
        $totalPagu = (int) $anggaranQuery->sum('total_pagu');

        $arusKas = [
            'debit' => (int) Transaksi::where('jenis','debit')->sum('amount'),
            'kredit' => (int) Transaksi::where('jenis','kredit')->sum('amount'),
        ];

        // Laporan pemasukan per kanal & per program (top 5)
        $periode = null;
        $filters = ['unit_id' => $unitId, 'periode_id' => $periodeId];
        if ($filters['periode_id']) { $periode = Periode::find($filters['periode_id']); }
        $incomeQ = \App\Models\Income::query()->with('program');
        if ($periode) {
            $incomeQ->whereBetween('tanggal', [$periode->start_date, $periode->end_date]);
        }
        if ($filters['unit_id']) {
            $incomeQ->where(function($q) use ($filters){
                $q->whereNull('program_id') // General Fund dihitung
                  ->orWhereHas('program', function($qq) use ($filters){ $qq->where('unit_id',$filters['unit_id']); });
            });
        }
        $incomeByChannel = (clone $incomeQ)
            ->select('channel', DB::raw('SUM(amount) as total'))
            ->groupBy('channel')->pluck('total','channel');
        $incomeByProgram = (clone $incomeQ)
            ->select(DB::raw("COALESCE(program_id, 0) as pid"), DB::raw('SUM(amount) as total'))
            ->groupBy('pid')->orderByDesc('total')->limit(5)->get();
        $programNames = \App\Models\Program::whereIn('id', $incomeByProgram->pluck('pid')->filter())->pluck('name','id');

        $recent = (clone $pengajuanQuery)->orderByDesc('id')->limit(5)->get(['id','kode','judul','status','total_diminta','created_at']);
        if ($recent->isEmpty()) {
            $recent = Disbursement::with(['program','beneficiary'])
                ->orderByDesc('id')->limit(5)
                ->get(['id','code','program_id','beneficiary_id','amount','status','updated_at'])
                ->map(function($d){
                    return (object) [
                        'kode' => $d->code,
                        'judul' => trim(('Penyaluran '.($d->program->name ?? '')).' kepada '.($d->beneficiary->name ?? '')),
                        'status' => $d->status,
                        'total_diminta' => (int)$d->amount,
                        'created_at' => $d->updated_at ?? now(),
                    ];
                });
        }

        $units = Unit::orderBy('name')->get(['id','name']);
        $periodes = Periode::orderByDesc('start_date')->get(['id','name']);

        return view('dashboard', [
            'filters' => [ 'unit_id' => $unitId, 'periode_id' => $periodeId ],
            'counts' => $counts,
            'totalPagu' => $totalPagu,
            'totalDimintaApproved' => $totalDimintaApproved,
            'sisaPagu' => max(0, $totalPagu - $totalDimintaApproved),
            'arusKas' => $arusKas,
            'recent' => $recent,
            'units' => $units,
            'periodes' => $periodes,
            'incomeByChannel' => $incomeByChannel,
            'incomeByProgram' => $incomeByProgram,
            'programNames' => $programNames,
        ]);
    }
}
