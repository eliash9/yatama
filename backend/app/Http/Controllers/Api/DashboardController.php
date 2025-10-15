<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\Pengajuan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $filters = [
            'unit_id' => $request->query('unit_id'),
            'periode_id' => $request->query('periode_id'),
        ];

        $pengajuanQuery = Pengajuan::query();
        if ($filters['unit_id']) $pengajuanQuery->where('unit_id', $filters['unit_id']);
        if ($filters['periode_id']) $pengajuanQuery->where('periode_id', $filters['periode_id']);

        $counts = $pengajuanQuery->clone()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->pluck('count','status');

        $totalDimintaApproved = $pengajuanQuery->clone()
            ->whereIn('status', ['disetujui','dicairkan','selesai'])
            ->sum('total_diminta');

        $anggaranQuery = Anggaran::query();
        if ($filters['unit_id']) $anggaranQuery->where('unit_id', $filters['unit_id']);
        if ($filters['periode_id']) $anggaranQuery->where('periode_id', $filters['periode_id']);
        $totalPagu = $anggaranQuery->sum('total_pagu');

        $arusKas = [
            'debit' => Transaksi::where('jenis','debit')->sum('amount'),
            'kredit' => Transaksi::where('jenis','kredit')->sum('amount'),
        ];

        return [
            'filters' => $filters,
            'pengajuan_counts' => $counts,
            'total_pagu' => (int) $totalPagu,
            'total_diminta_approved' => (int) $totalDimintaApproved,
            'sisa_pagu' => (int) max(0, $totalPagu - $totalDimintaApproved),
            'arus_kas' => $arusKas,
            'recent_pengajuan' => $pengajuanQuery->clone()->orderByDesc('id')->limit(5)->get(['id','kode','judul','status','total_diminta','created_at']),
        ];
    }
}

