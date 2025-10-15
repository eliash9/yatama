<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Donor;
use Faker\Factory as Faker;

class DonorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        for ($i=0; $i<25; $i++) {
            Donor::firstOrCreate(
                ['code' => 'D'.str_pad((string)($i+1),4,'0',STR_PAD_LEFT)],
                [
                    'type' => $faker->randomElement(['individual','company']),
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'phone' => $faker->phoneNumber(),
                    'address' => $faker->address(),
                    'is_active' => true,
                ]
            );
        }
    }
}

