<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\Pengajuan;
use App\Models\Transaksi;
use App\Models\Unit;
use App\Models\Periode;
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

        $counts = (clone $pengajuanQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->pluck('count','status')->toArray();

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
                $q->whereNull('program_id') // General Fund tetap dihitung
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
