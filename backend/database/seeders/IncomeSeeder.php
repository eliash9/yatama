<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Income;
use App\Models\Donor;
use App\Models\Program;
use Faker\Factory as Faker;

class IncomeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $donorIds = Donor::pluck('id')->all();
        $programIds = Program::pluck('id')->all();
        $channels = ['transfer','qris','va','tunai','gateway'];

        for ($i=0; $i<120; $i++) {
            $dt = $faker->dateTimeBetween('-6 months', 'now');
            $receipt = 'KW-'.$dt->format('Ymd').'-'.str_pad((string)$i,4,'0',STR_PAD_LEFT);
            Income::updateOrCreate(
                ['receipt_no'=>$receipt],
                [
                    'tanggal' => $dt->format('Y-m-d'),
                    'channel' => $faker->randomElement($channels),
                    'amount' => $faker->numberBetween(25000, 5000000),
                    'donor_id' => $faker->optional(0.9)->randomElement($donorIds),
                    'program_id' => $faker->optional(0.6)->randomElement($programIds),
                    'status' => $faker->randomElement(['recorded','matched']),
                    'ref_no' => strtoupper($faker->bothify('REF####')),
                    'notes' => $faker->sentence(6),
                ]
            );
        }
    }
}

