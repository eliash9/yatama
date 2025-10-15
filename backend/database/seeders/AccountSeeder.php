<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code'=>'CASH', 'name'=>'Kas Kecil', 'type'=>'cash', 'opening_balance'=>500000],
            ['code'=>'BCA-001', 'name'=>'Bank BCA Operasional', 'type'=>'bank', 'bank_name'=>'BCA', 'account_no'=>'1234567890', 'opening_balance'=>20000000],
            ['code'=>'MANDIRI-002', 'name'=>'Bank Mandiri Program', 'type'=>'bank', 'bank_name'=>'Mandiri', 'account_no'=>'9876543210', 'opening_balance'=>10000000],
        ];
        foreach ($rows as $r) {
            Account::firstOrCreate(['code'=>$r['code']], $r);
        }
    }
}

