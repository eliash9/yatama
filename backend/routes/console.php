<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('app:demo-data {--fresh}', function () {
    $fresh = (bool) $this->option('fresh');
    if ($fresh) {
        $this->info('Refreshing database...');
        Artisan::call('migrate:fresh');
        $this->line(Artisan::output());
    }

    $this->info('Seeding demo data...');
    $seeders = [
        'Database\\Seeders\\UnitSeeder',
        'Database\\Seeders\\PeriodeSeeder',
        'Database\\Seeders\\AccountSeeder',
        'Database\\Seeders\\ProgramSeeder',
        'Database\\Seeders\\DonorSeeder',
        'Database\\Seeders\\BeneficiarySeeder',
        'Database\\Seeders\\VolunteerSeeder',
        'Database\\Seeders\\IncomeSeeder',
        'Database\\Seeders\\TransactionSeeder',
        'Database\\Seeders\\DisbursementSeeder',
    ];
    foreach ($seeders as $cls) {
        Artisan::call('db:seed', ['--class' => $cls]);
        $this->line("Seeded: $cls");
    }

    $this->info('Demo data ready.');
})->purpose('Generate or refresh demo data');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
