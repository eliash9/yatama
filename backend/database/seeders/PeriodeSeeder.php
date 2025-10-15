<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Periode;

class PeriodeSeeder extends Seeder
{
    public function run(): void
    {
        $y = (int) date('Y');
        $periods = [
            ['code'=>"$y", 'name'=>"Tahun $y", 'start_date'=>"$y-01-01", 'end_date'=>"$y-12-31", 'is_locked'=>false],
        ];
        foreach ($periods as $p) {
            Periode::firstOrCreate(['code'=>$p['code']], $p);
        }
    }
}

