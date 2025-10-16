<?php

namespace App\Exports;

use App\Models\Transaksi;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CashflowExport implements FromArray, WithHeadings, Responsable
{
    public string $fileName = 'cashflow.xlsx';

    public function __construct(protected ?string $from = null, protected ?string $to = null)
    {
    }

    public function array(): array
    {
        $driver = DB::connection()->getDriverName();
        $q = Transaksi::query();
        if ($this->from) $q->where('tanggal','>=',$this->from);
        if ($this->to) $q->where('tanggal','<=',$this->to);
        try {
            if ($driver === 'mysql' || $driver === 'mariadb') {
                $series = $q->select(DB::raw("DATE_FORMAT(tanggal,'%Y-%m') as ym"),
                        DB::raw("SUM(CASE WHEN jenis='debit' THEN amount ELSE 0 END) as debit"),
                        DB::raw("SUM(CASE WHEN jenis='kredit' THEN amount ELSE 0 END) as kredit"))
                    ->groupBy('ym')->orderBy('ym')->get();
            } elseif ($driver === 'sqlite') {
                $series = $q->select(DB::raw("strftime('%Y-%m', tanggal) as ym"),
                        DB::raw("SUM(CASE WHEN jenis='debit' THEN amount ELSE 0 END) as debit"),
                        DB::raw("SUM(CASE WHEN jenis='kredit' THEN amount ELSE 0 END) as kredit"))
                    ->groupBy('ym')->orderBy('ym')->get();
            } elseif ($driver === 'pgsql') {
                $series = $q->select(DB::raw("to_char(tanggal, 'YYYY-MM') as ym"),
                        DB::raw("SUM(CASE WHEN jenis='debit' THEN amount ELSE 0 END) as debit"),
                        DB::raw("SUM(CASE WHEN jenis='kredit' THEN amount ELSE 0 END) as kredit"))
                    ->groupBy('ym')->orderBy('ym')->get();
            } else { throw new \RuntimeException('Unsupported'); }
        } catch (\Throwable $e) {
            $rows = $q->get(['tanggal','jenis','amount']);
            $map = [];
            foreach ($rows as $row) {
                $ym = substr((string)$row->tanggal, 0, 7);
                if (!isset($map[$ym])) $map[$ym] = ['ym'=>$ym,'debit'=>0,'kredit'=>0];
                if ($row->jenis === 'debit') $map[$ym]['debit'] += (int)$row->amount; else $map[$ym]['kredit'] += (int)$row->amount;
            }
            ksort($map);
            $series = collect(array_values(array_map(fn($v)=>(object)$v, $map)));
        }
        $rows = [];
        foreach ($series as $r) { $rows[] = [$r->ym, (int)$r->debit, (int)$r->kredit]; }
        return $rows;
    }

    public function headings(): array
    {
        return ['Periode','Debit','Kredit'];
    }
}
