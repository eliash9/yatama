<?php

namespace App\Exports;

use App\Models\Transaksi;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OperationalRatioExport implements FromArray, WithHeadings, Responsable
{
    public string $fileName = 'operational_ratio.xlsx';

    public function __construct(protected ?string $from = null, protected ?string $to = null)
    {
    }

    public function array(): array
    {
        $ops = Transaksi::when($this->from, fn($q)=>$q->where('tanggal','>=',$this->from))
            ->when($this->to, fn($q)=>$q->where('tanggal','<=',$this->to))
            ->where('jenis','kredit')->where('category','operational')->sum('amount');
        $programSpend = Transaksi::when($this->from, fn($q)=>$q->where('tanggal','>=',$this->from))
            ->when($this->to, fn($q)=>$q->where('tanggal','<=',$this->to))
            ->where('jenis','kredit')->whereNotNull('program_id')->sum('amount');
        $total = max(1, (int)$ops + (int)$programSpend);
        $ratio = round(((int)$ops / $total) * 100, 2);
        return [
            ['Kategori','Jumlah'],
            ['Belanja Operasional', (int)$ops],
            ['Belanja Program', (int)$programSpend],
            ['Rasio Operasional (%)', $ratio],
        ];
    }

    public function headings(): array
    {
        return ['Kategori','Jumlah'];
    }
}

