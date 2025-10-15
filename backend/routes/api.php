<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

require __DIR__.'/auth.php';

use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\PeriodeController;
use App\Http\Controllers\Api\AnggaranController;
use App\Http\Controllers\Api\PengajuanController;
use App\Http\Controllers\Api\PencairanController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DonorController;
use App\Http\Controllers\Api\BeneficiaryController;
use App\Http\Controllers\Api\VolunteerController;
use App\Http\Controllers\Api\ProgramController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('units', UnitController::class);
    Route::apiResource('periodes', PeriodeController::class);

    // Anggaran
    Route::get('anggarans', [AnggaranController::class, 'index']);
    Route::post('anggarans', [AnggaranController::class, 'store']);
    Route::get('anggarans/{anggaran}', [AnggaranController::class, 'show']);
    Route::patch('anggarans/{anggaran}', [AnggaranController::class, 'update']);
    Route::post('anggarans/{anggaran}/items', [AnggaranController::class, 'addItem']);
    Route::post('anggarans/{anggaran}/finalize', [AnggaranController::class, 'finalize']);

    // Pengajuan
    Route::get('pengajuans', [PengajuanController::class, 'index']);
    Route::post('pengajuans', [PengajuanController::class, 'store']);
    Route::get('pengajuans/{pengajuan}', [PengajuanController::class, 'show']);
    Route::patch('pengajuans/{pengajuan}', [PengajuanController::class, 'update']);
    Route::post('pengajuans/{pengajuan}/submit', [PengajuanController::class, 'submit']);
    Route::post('pengajuans/{pengajuan}/approvals', [PengajuanController::class, 'decide']);

    // Pencairan
    Route::post('pengajuans/{pengajuan}/pencairans', [PencairanController::class, 'store']);

    // Transaksi
    Route::get('transaksi', [TransaksiController::class, 'index']);
    Route::post('transaksi', [TransaksiController::class, 'store']);

    // Dashboard
    Route::get('dashboard', DashboardController::class);

    // Master Data
    Route::apiResource('donors', DonorController::class);
    Route::apiResource('beneficiaries', BeneficiaryController::class);
    Route::apiResource('volunteers', VolunteerController::class);
    Route::apiResource('programs', ProgramController::class);
});
