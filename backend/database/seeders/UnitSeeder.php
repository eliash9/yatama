<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code'=>'HQ','name'=>'Kantor Pusat'],
            ['code'=>'CBG-JKT','name'=>'Cabang Jakarta'],
            ['code'=>'CBG-SBY','name'=>'Cabang Surabaya'],
        ];
        foreach ($units as $u) {
            Unit::firstOrCreate(['code'=>$u['code']], $u);
        }
    }
}

