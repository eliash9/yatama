<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Disbursement;
use App\Models\DisbursementPayment;
use App\Models\Program;
use App\Models\Beneficiary;
use App\Models\Account;
use Faker\Factory as Faker;

class DisbursementSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $programIds = Program::pluck('id')->all();
        $benefIds = Beneficiary::pluck('id')->all();
        $account = Account::first();

        for ($i=0; $i<30; $i++) {
            $code = 'DSB-'.date('Y').'-'.str_pad((string)$i,4,'0',STR_PAD_LEFT);
            $status = $faker->randomElement(['draft','submitted','assessed','program_verified','finance_verified','approved','paid']);

            $row = Disbursement::updateOrCreate(
                ['code' => $code],
                [
                    'program_id' => $faker->randomElement($programIds),
                    'beneficiary_id' => $faker->randomElement($benefIds),
                    'requested_by' => 1,
                    'amount' => $faker->numberBetween(100000, 3000000),
                    'method_preference' => $faker->randomElement(['cash','transfer','ewallet']),
                    'purpose' => $faker->sentence(6),
                    'status' => $status,
                ]
            );

            if ($row->status === 'paid' && !$row->payments()->exists()) {
                DisbursementPayment::create([
                    'disbursement_id'=>$row->id,
                    'channel'=>'transfer',
                    'account_id'=>$account?->id,
                    'amount'=>$row->amount,
                    'paid_at'=>now()->subDays($faker->numberBetween(1,90)),
                    'recipient_name'=>'Beneficiary',
                    'bank_name'=>'BCA','account_no'=>'1234567890','ref_no'=>'REF'.$i,
                    'created_by'=>1,
                ]);
            }
        }
    }
}
