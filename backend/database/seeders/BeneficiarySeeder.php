<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Beneficiary;
use Faker\Factory as Faker;

class BeneficiarySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $regions = ['Jakarta','Jawa Barat','Jawa Timur','Banten','DIY'];
        for ($i=0; $i<40; $i++) {
            Beneficiary::firstOrCreate(
                ['code' => 'B'.str_pad((string)($i+1),4,'0',STR_PAD_LEFT)],
                [
                    'type' => $faker->randomElement(['anak','keluarga','panti']),
                    'name' => $faker->name(),
                    'date_of_birth' => $faker->date(),
                    'guardian_name' => $faker->optional()->name(),
                    'email' => $faker->optional()->safeEmail(),
                    'phone' => $faker->optional()->phoneNumber(),
                    'address' => $faker->address(),
                    'region' => $faker->randomElement($regions),
                    'is_active' => true,
                ]
            );
        }
    }
}

