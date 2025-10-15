<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Volunteer;
use Faker\Factory as Faker;

class VolunteerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        for ($i=0; $i<20; $i++) {
            Volunteer::firstOrCreate(
                ['code' => 'V'.str_pad((string)($i+1),4,'0',STR_PAD_LEFT)],
                [
                    'name' => $faker->name(),
                    'email' => $faker->optional()->safeEmail(),
                    'phone' => $faker->optional()->phoneNumber(),
                    'address' => $faker->address(),
                    'skills' => $faker->randomElement(['Lapangan','Administrasi','IT','Kesehatan']),
                    'joined_at' => $faker->date(),
                    'is_active' => true,
                ]
            );
        }
    }
}

