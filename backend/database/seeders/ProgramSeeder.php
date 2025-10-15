<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Unit;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $unit = Unit::where('code','HQ')->first() ?? Unit::first();
        $rows = [
            ['code'=>'BEASISWA','name'=>'Beasiswa','category'=>'Pendidikan','type'=>'program','unit_id'=>$unit?->id,'target_amount'=>50000000],
            ['code'=>'SANTUNAN','name'=>'Santunan Rutin','category'=>'Sosial','type'=>'program','unit_id'=>$unit?->id,'target_amount'=>30000000],
            ['code'=>'KESEHATAN','name'=>'Bantuan Kesehatan','category'=>'Kesehatan','type'=>'program','unit_id'=>$unit?->id,'target_amount'=>40000000],
        ];
        foreach ($rows as $r) {
            Program::firstOrCreate(['code'=>$r['code']], $r);
        }
    }
}

