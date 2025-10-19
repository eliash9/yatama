<?php

namespace App\Exports;

use App\Models\Program;
use App\Models\Income;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CampaignsExport implements FromArray, WithHeadings, Responsable
{
    public string $fileName = 'campaigns.xlsx';

    public function array(): array
    {
        $rows = [];
        $programs = Program::orderBy('name')->get(['id','name']);
        $totals = Income::select('program_id', DB::raw('SUM(amount) as total'))->where(function(){->where('status','matched')->orWhere('channel','tunai');})->whereNotNull('program_id')->groupBy('program_id')->pluck('total','program_id');
        foreach ($programs as $p) {
            $rows[] = [$p->name, (int)($totals[$p->id] ?? 0)];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Program','Total Penerimaan'];
    }
}


