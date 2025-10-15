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
