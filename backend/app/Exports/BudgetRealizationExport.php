<?php

namespace App\Exports;

use App\Models\Anggaran;
use App\Models\PengajuanItem;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BudgetRealizationExport implements FromArray, WithHeadings, Responsable
{
    public string $fileName = 'budget_realization.xlsx';

    public function __construct(protected ?int $unitId = null, protected ?int $periodeId = null)
    {
    }

    public function array(): array
    {
        if (!$this->unitId || !$this->periodeId) return [];
        $anggaran = Anggaran::with('items')->where('unit_id',$this->unitId)->where('periode_id',$this->periodeId)->first();
        $paguByAcc = collect();
        if ($anggaran) {
            $paguByAcc = $anggaran->items->groupBy('account_code')->map(fn($g)=> (int) $g->sum('pagu'));
        }
        $approved = ['disetujui','dicairkan','selesai'];
        $realByAcc = PengajuanItem::select('pengajuan_items.account_code', DB::raw('SUM(pengajuan_items.subtotal) as total'))
            ->join('pengajuans','pengajuans.id','=','pengajuan_items.pengajuan_id')
            ->where('pengajuans.unit_id',$this->unitId)
            ->where('pengajuans.periode_id',$this->periodeId)
            ->whereIn('pengajuans.status',$approved)
            ->groupBy('pengajuan_items.account_code')
            ->pluck('total','account_code');
        $codes = $paguByAcc->keys()->merge(collect($realByAcc)->keys())->unique()->sort();
        $rows = [];
        foreach ($codes as $code) {
            $pagu = (int) ($paguByAcc[$code] ?? 0);
            $real = (int) ($realByAcc[$code] ?? 0);
            $rows[] = [$code, $pagu, $real, max(0,$pagu-$real)];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Kode Akun','Pagu','Realisasi','Sisa'];
    }
}

