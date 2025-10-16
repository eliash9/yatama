<?php

namespace App\Exports;

use App\Models\Disbursement;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DisbursementsExport implements FromCollection, WithHeadings, WithMapping, Responsable
{
    public string $fileName = 'disbursements.xlsx';

    public function __construct(protected ?string $from = null, protected ?string $to = null)
    {
    }

    public function collection()
    {
        return Disbursement::with(['program','beneficiary'])
            ->when($this->from, fn($q)=>$q->whereDate('created_at','>=',$this->from))
            ->when($this->to, fn($q)=>$q->whereDate('created_at','<=',$this->to))
            ->orderByDesc('id')
            ->get();
    }

    public function headings(): array
    {
        return ['Kode','Program','Penerima','Jumlah','Status','Tanggal'];
    }

    public function map($r): array
    {
        return [
            $r->code,
            $r->program->name ?? '',
            $r->beneficiary->name ?? '',
            (int) $r->amount,
            $r->status,
            optional($r->created_at)->format('Y-m-d'),
        ];
    }
}

