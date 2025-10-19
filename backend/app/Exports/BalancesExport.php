<?php

namespace App\Exports;

use App\Models\Account;
use App\Models\Program;
use App\Models\Transaksi;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BalancesExport implements FromArray, WithHeadings, Responsable
{
    public string $fileName = 'balances.xlsx';

    public function array(): array
    {
        $rows = [];
        // Account balances
        $accounts = Account::orderBy('name')->get();
        foreach ($accounts as $a) {
            $net = Transaksi::where('account_id',$a->id)
                ->select(DB::raw("COALESCE(SUM(CASE WHEN jenis='debit' THEN amount ELSE -amount END),0) as net"))
                ->value('net') ?? 0;
            $rows[] = ['Account', $a->name, (int)$a->opening_balance + (int)$net];
        }
        // Program balances (incomes - spends)
        $incomes = \App\Models\Income::select('program_id', DB::raw('SUM(amount) as total'))->where(function(){->where('status','matched')->orWhere('channel','tunai');})
            ->groupBy('program_id')->pluck('total','program_id');
        $spends = Transaksi::whereNotNull('program_id')
            ->select('program_id', DB::raw("SUM(CASE WHEN jenis='debit' THEN amount ELSE -amount END) as total"))
            ->groupBy('program_id')->pluck('total','program_id');
        $programs = Program::orderBy('name')->get(['id','name']);
        foreach ($programs as $p) {
            $in = (int) ($incomes[$p->id] ?? 0);
            $out = (int) ($spends[$p->id] ?? 0);
            $rows[] = ['Program', $p->name, $in + $out];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Tipe','Nama','Saldo'];
    }
}


