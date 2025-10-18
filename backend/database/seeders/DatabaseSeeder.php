<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles
        $roles = collect(['admin', 'bendahara', 'pimpinan', 'unit']);
        $roles->each(fn ($r) => Role::findOrCreate($r, 'web'));

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');
        //bendahara
        $bendahara = User::firstOrCreate(
            ['email' => 'bendahara@example.com'],
            [
                'name' => 'Bendahara',
                'password' => Hash::make('password'),
            ]
        );
        $bendahara->assignRole('bendahara');
        //pimpinan
        $pimpinan = User::firstOrCreate(
            ['email' => 'pimpinan@example.com'],
            [
                'name' => 'Pimpinan',
                'password' => Hash::make('password'),
            ]
        );
        $pimpinan->assignRole('pimpinan');
        //unit
        $unit = User::firstOrCreate(
            ['email' => 'unit@example.com'],
            [
                'name' => 'Unit',
                'password' => Hash::make('password'),
            ]
        );
        $unit->assignRole('unit');
        

        // Demo data
        $this->call([
            UnitSeeder::class,
            PeriodeSeeder::class,
            AccountSeeder::class,
            ProgramSeeder::class,
            DonorSeeder::class,
            BeneficiarySeeder::class,
            VolunteerSeeder::class,
            IncomeSeeder::class,
            TransactionSeeder::class,
            DisbursementSeeder::class,
        ]);
    }
}
