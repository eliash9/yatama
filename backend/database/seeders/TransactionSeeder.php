<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\Account;
use App\Models\Program;
use App\Models\Income;
use Faker\Factory as Faker;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $activeAccounts = Account::where('is_active', true)->get(['id','code','type']);
        $accountIds = $activeAccounts->pluck('id')->all();
        $programIds = Program::pluck('id')->all();

        // Map channels to reasonable default accounts
        $kasTunai = Account::where('code','1.1.1')->where('is_active',true)->first();
        $bankAccounts = Account::where('is_active',true)->where('code','like','1.1.2.%')->get(['id','code']);
        if ($bankAccounts->isEmpty()) { $bankAccounts = $activeAccounts; }

        // Record debit inflows based on incomes (so balances look realistic)
        foreach (Income::orderBy('tanggal')->get(['id','tanggal','amount','program_id','channel']) as $inc) {
            // Choose account based on channel
            $acc = null;
            if ($inc->channel === 'tunai' && $kasTunai) {
                $acc = $kasTunai;
            } else {
                $acc = $bankAccounts->random();
            }
            Transaksi::updateOrCreate(
                ['ref_type'=>'income','ref_id'=>$inc->id],
                [
                    'tanggal'    => $inc->tanggal,
                    'jenis'      => 'debit',
                    'akun_kas'   => $acc ? $acc->code : 'UNKNOWN',
                    'account_id' => $acc ? $acc->id : null,
                    'amount'     => $inc->amount,
                    'program_id' => $inc->program_id, // earmarked if exists
                    'category'   => $inc->program_id ? 'program' : 'general',
                    'memo'       => 'Penerimaan donasi (sinkron income)'
                ]
            );
        }

        // Operational expenses (kredit)
        for ($i=0; $i<60; $i++) {
            $dt = $faker->dateTimeBetween('-6 months', 'now');
            // choose a random active account and use its code as label
            $accId = $faker->randomElement($accountIds);
            $accCode = optional($activeAccounts->firstWhere('id',$accId))->code ?? 'UNKNOWN';
            Transaksi::create([
                'tanggal' => $dt->format('Y-m-d'),
                'jenis' => 'kredit',
                'akun_kas' => $accCode,
                'account_id' => $accId,
                'amount' => $faker->numberBetween(50000, 2000000),
                'category' => 'operational',
                'memo' => 'Biaya operasional '.$faker->word(),
            ]);
        }

        // Program expenses (kredit) tagged to program
        for ($i=0; $i<60; $i++) {
            $dt = $faker->dateTimeBetween('-6 months', 'now');
            $accId = $faker->randomElement($accountIds);
            $accCode = optional($activeAccounts->firstWhere('id',$accId))->code ?? 'UNKNOWN';
            Transaksi::create([
                'tanggal' => $dt->format('Y-m-d'),
                'jenis' => 'kredit',
                'akun_kas' => $accCode,
                'account_id' => $accId,
                'amount' => $faker->numberBetween(50000, 3000000),
                'program_id' => $faker->randomElement($programIds),
                'category' => 'program',
                'memo' => 'Pengeluaran program '.$faker->word(),
            ]);
        }
    }
}
