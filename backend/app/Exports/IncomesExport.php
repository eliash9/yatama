<?php

namespace App\Exports;

use App\Models\Income;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IncomesExport implements FromCollection, WithHeadings, WithMapping, Responsable
{
    public string $fileName = 'incomes.xlsx';

    public function __construct(protected ?string $from = null, protected ?string $to = null)
    {
    }

    public function collection()
    {
        return Income::with(['donor','program'])
            ->when($this->from, fn($q)=>$q->where('tanggal','>=',$this->from))
            ->when($this->to, fn($q)=>$q->where('tanggal','<=',$this->to))
            ->orderByDesc('tanggal')
            ->get();
    }

    public function headings(): array
    {
        return ['Tanggal','No Kwitansi','Kanal','Donatur','Program','Jumlah','Status','Ref'];
    }

    public function map($r): array
    {
        return [
            $r->tanggal,
            $r->receipt_no,
            $r->channel,
            $r->donor->name ?? '',
            $r->program->name ?? 'General Fund',
            (int) $r->amount,
            $r->status,
            $r->ref_no,
        ];
    }
}

