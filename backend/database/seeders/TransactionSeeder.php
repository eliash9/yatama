<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\Account;
use App\Models\Program;
use Faker\Factory as Faker;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $accountIds = Account::pluck('id')->all();
        $programIds = Program::pluck('id')->all();

        // Operational expenses (kredit)
        for ($i=0; $i<60; $i++) {
            $dt = $faker->dateTimeBetween('-6 months', 'now');
            Transaksi::create([
                'tanggal' => $dt->format('Y-m-d'),
                'jenis' => 'kredit',
                'akun_kas' => 'OPS',
                'account_id' => $faker->randomElement($accountIds),
                'amount' => $faker->numberBetween(50000, 2000000),
                'category' => 'operational',
                'memo' => 'Biaya operasional '.$faker->word(),
            ]);
        }

        // Program expenses (kredit) tagged to program
        for ($i=0; $i<60; $i++) {
            $dt = $faker->dateTimeBetween('-6 months', 'now');
            Transaksi::create([
                'tanggal' => $dt->format('Y-m-d'),
                'jenis' => 'kredit',
                'akun_kas' => 'PRG',
                'account_id' => $faker->randomElement($accountIds),
                'amount' => $faker->numberBetween(50000, 3000000),
                'program_id' => $faker->randomElement($programIds),
                'category' => 'program',
                'memo' => 'Pengeluaran program '.$faker->word(),
            ]);
        }
    }
}

