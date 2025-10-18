<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        // Chart of Accounts (simplified) for cash/bank usage
        // Parents are seeded as inactive for reference/grouping
        $rows = [
            // A. Aset
            ['code'=>'1', 'name'=>'Aset', 'type'=>'cash', 'is_active'=>false],
            ['code'=>'1.1', 'name'=>'Kas', 'type'=>'cash', 'is_active'=>false],
            ['code'=>'1.1.1', 'name'=>'Kas Tunai', 'type'=>'cash', 'opening_balance'=>0, 'is_active'=>true],
            ['code'=>'1.1.2', 'name'=>'Kas di Bank', 'type'=>'bank', 'is_active'=>false],
            // Sub rekening bank (contoh umum)
            ['code'=>'1.1.2.1', 'name'=>'Bank BCA Operasional', 'type'=>'bank', 'bank_name'=>'BCA', 'account_no'=>'1234567890', 'opening_balance'=>20000000, 'is_active'=>true],
            ['code'=>'1.1.2.2', 'name'=>'Bank Mandiri Program', 'type'=>'bank', 'bank_name'=>'Mandiri', 'account_no'=>'9876543210', 'opening_balance'=>10000000, 'is_active'=>true],
            ['code'=>'1.1.3', 'name'=>'Kas Dana Terikat', 'type'=>'bank', 'is_active'=>false],
            // Sub rekening dana terikat (contoh, dapat ditambah sesuai kebutuhan)
            ['code'=>'1.1.3.1', 'name'=>'Kas Dana Terikat - Beasiswa', 'type'=>'bank', 'opening_balance'=>0, 'is_active'=>true],
            ['code'=>'1.1.3.2', 'name'=>'Kas Dana Terikat - Pembangunan Masjid', 'type'=>'bank', 'opening_balance'=>0, 'is_active'=>true],

            ['code'=>'1.2', 'name'=>'Piutang', 'type'=>'cash', 'is_active'=>false],
            ['code'=>'1.2.1', 'name'=>'Piutang Donasi (Janji Donatur)', 'type'=>'cash', 'is_active'=>false],
            ['code'=>'1.2.2', 'name'=>'Piutang Usaha', 'type'=>'cash', 'is_active'=>false],

            ['code'=>'1.3', 'name'=>'Aset Tetap', 'type'=>'cash', 'is_active'=>false],
            ['code'=>'1.3.1', 'name'=>'Tanah & Bangunan Asrama', 'type'=>'cash', 'is_active'=>false],
            ['code'=>'1.3.2', 'name'=>'Kendaraan Operasional', 'type'=>'cash', 'is_active'=>false],
            ['code'=>'1.3.3', 'name'=>'Peralatan & Inventaris', 'type'=>'cash', 'is_active'=>false],

            // B. Kewajiban
            ['code'=>'2', 'name'=>'Kewajiban', 'type'=>'bank', 'is_active'=>false],
            ['code'=>'2.1', 'name'=>'Utang Usaha', 'type'=>'bank', 'is_active'=>false],
            ['code'=>'2.2', 'name'=>'Utang Gaji & Honor Relawan', 'type'=>'bank', 'is_active'=>false],
            ['code'=>'2.3', 'name'=>'Dana Amanah Belum Tersalurkan', 'type'=>'bank', 'is_active'=>false],

            // C. Ekuitas / Dana
            ['code'=>'3', 'name'=>'Ekuitas / Dana', 'type'=>'bank', 'is_active'=>false],
            ['code'=>'3.1', 'name'=>'Dana Tidak Terikat (Umum)', 'type'=>'bank', 'is_active'=>false],
            ['code'=>'3.2', 'name'=>'Dana Terikat Temporer', 'type'=>'bank', 'is_active'=>false],
            ['code'=>'3.3', 'name'=>'Dana Terikat Permanen', 'type'=>'bank', 'is_active'=>false],
        ];
        foreach ($rows as $r) {
            Account::firstOrCreate(['code'=>$r['code']], $r);
        }
    }
}
