<?php

namespace App\Exports;

use App\Models\Income;
use App\Models\Transaksi;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FundsExport implements FromArray, WithHeadings, Responsable
{
    public string $fileName = 'funds.xlsx';

    public function __construct(protected ?string $from = null, protected ?string $to = null)
    {
    }

    public function array(): array
    {
        $inc = Income::query()->where(function(){->where('status','matched')->orWhere('channel','tunai');}); if ($this->from) $inc->where('tanggal','>=',$this->from); if ($this->to) $inc->where('tanggal','<=',$this->to);
        $earmark = (int) (clone $inc)->whereNotNull('program_id')->sum('amount');
        $general = (int) (clone $inc)->whereNull('program_id')->sum('amount');
        $spendProgram = (int) Transaksi::whereNotNull('program_id')
            ->when($this->from, fn($q)=>$q->where('tanggal','>=',$this->from))
            ->when($this->to, fn($q)=>$q->where('tanggal','<=',$this->to))
            ->where('jenis','kredit')->sum('amount');
        $spendGeneral = (int) Transaksi::whereNull('program_id')
            ->when($this->from, fn($q)=>$q->where('tanggal','>=',$this->from))
            ->when($this->to, fn($q)=>$q->where('tanggal','<=',$this->to))
            ->where('jenis','kredit')->sum('amount');
        return [
            ['Kategori','Jumlah'],
            ['Earmark Masuk', $earmark],
            ['General Masuk', $general],
            ['Earmark Keluar', $spendProgram],
            ['General Keluar', $spendGeneral],
        ];
    }

    public function headings(): array
    {
        return ['Kategori','Jumlah'];
    }
}


