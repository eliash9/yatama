<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardPageController;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Master\DonorPageController;
use App\Http\Controllers\Master\BeneficiaryPageController;
use App\Http\Controllers\Master\VolunteerPageController;
use App\Http\Controllers\Master\ProgramPageController;
use App\Http\Controllers\Master\AccountPageController;
use App\Http\Controllers\Finance\IncomePageController;
use App\Http\Controllers\Finance\BankMutationPageController;
use App\Http\Controllers\Finance\TransactionPageController;
use App\Http\Controllers\Finance\CashbookPageController;
use App\Http\Controllers\Finance\ReportPageController;
use App\Http\Controllers\Finance\DisbursementPageController;
use App\Http\Controllers\Admin\DemoController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;

Route::get('/', function () { return redirect('/dashboard'); });

Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);
});

Route::post('/logout', [WebAuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/dashboard', DashboardPageController::class)->middleware('auth')->name('dashboard');
// Hindari memuat routes auth API pada web routes.

Route::prefix('master')->name('master.')->middleware('auth')->group(function(){
    Route::resource('donors', DonorPageController::class);
    Route::resource('beneficiaries', BeneficiaryPageController::class)->except(['show']);
    Route::resource('volunteers', VolunteerPageController::class)->except(['show']);
    Route::resource('programs', ProgramPageController::class)->except(['show']);
    Route::resource('accounts', AccountPageController::class)->except(['show']);
});

// Guard roles for finance module
Route::prefix('finance')->name('finance.')->middleware(['auth','role:admin|bendahara|pimpinan'])->group(function(){
    Route::resource('incomes', IncomePageController::class);
    Route::get('incomes/{income}/receipt', [IncomePageController::class,'receipt'])->name('incomes.receipt');
    Route::get('mutations', [BankMutationPageController::class,'index'])->name('mutations.index');
    Route::post('mutations/import', [BankMutationPageController::class,'import'])->name('mutations.import');
    Route::post('mutations/automatch', [BankMutationPageController::class,'automatch'])->name('mutations.automatch');
    Route::post('mutations/{mutation}/match', [BankMutationPageController::class,'match'])->name('mutations.match');
    Route::resource('transactions', TransactionPageController::class)->only(['index','create','store','destroy'])->middleware(['role:admin|bendahara']);
    Route::get('cashbook', CashbookPageController::class)->name('cashbook');
    Route::resource('disbursements', DisbursementPageController::class);
    Route::post('disbursements/{disbursement}/submit', [DisbursementPageController::class,'submit'])->name('disbursements.submit')->middleware(['role:unit|admin|bendahara|pimpinan']);
    Route::post('disbursements/{disbursement}/assess', [DisbursementPageController::class,'assess'])->name('disbursements.assess')->middleware(['role:unit|admin|bendahara|pimpinan']);
    Route::post('disbursements/{disbursement}/verify-program', [DisbursementPageController::class,'verifyProgram'])->name('disbursements.verify_program')->middleware(['role:admin|pimpinan']);
    Route::post('disbursements/{disbursement}/verify-finance', [DisbursementPageController::class,'verifyFinance'])->name('disbursements.verify_finance')->middleware(['role:admin|bendahara']);
    Route::post('disbursements/{disbursement}/approve', [DisbursementPageController::class,'approve'])->name('disbursements.approve')->middleware(['role:admin|pimpinan']);
    Route::post('disbursements/{disbursement}/pay', [DisbursementPageController::class,'pay'])->name('disbursements.pay')->middleware(['role:admin|bendahara']);
});

// Reports (read-only for unit & others)
Route::prefix('finance')->name('finance.')->middleware(['auth','role:admin|bendahara|pimpinan|unit'])->group(function(){
    Route::get('reports/balances', [ReportPageController::class,'balances'])->name('reports.balances');
    Route::get('reports/balance-sheet', [ReportPageController::class,'balanceSheet'])->name('reports.balance_sheet');
    Route::get('reports/balances.xlsx', [ReportPageController::class,'balancesExcel'])->name('reports.balances.xlsx');
    Route::get('reports/balances.pdf', [ReportPageController::class,'balancesPdf'])->name('reports.balances.pdf');
    Route::get('reports/incomes', [ReportPageController::class,'incomes'])->name('reports.incomes');
    Route::get('reports/incomes.csv', [ReportPageController::class,'incomesCsv'])->name('reports.incomes.csv');
    Route::get('reports/incomes.xlsx', [ReportPageController::class,'incomesExcel'])->name('reports.incomes.xlsx');
    Route::get('reports/incomes.pdf', [ReportPageController::class,'incomesPdf'])->name('reports.incomes.pdf');
    Route::get('reports/disbursements', [ReportPageController::class,'disbursements'])->name('reports.disbursements');
    Route::get('reports/disbursements.csv', [ReportPageController::class,'disbursementsCsv'])->name('reports.disbursements.csv');
    Route::get('reports/disbursements.xlsx', [ReportPageController::class,'disbursementsExcel'])->name('reports.disbursements.xlsx');
    Route::get('reports/disbursements.pdf', [ReportPageController::class,'disbursementsPdf'])->name('reports.disbursements.pdf');
    Route::get('reports/cashflow', [ReportPageController::class,'cashflow'])->name('reports.cashflow');
    Route::get('reports/cashflow.csv', [ReportPageController::class,'cashflowCsv'])->name('reports.cashflow.csv');
    Route::get('reports/cashflow.xlsx', [ReportPageController::class,'cashflowExcel'])->name('reports.cashflow.xlsx');
    Route::get('reports/cashflow.pdf', [ReportPageController::class,'cashflowPdf'])->name('reports.cashflow.pdf');
    Route::get('reports/funds', [ReportPageController::class,'funds'])->name('reports.funds');
    Route::get('reports/activity', [ReportPageController::class,'activity'])->name('reports.activity');
    Route::get('reports/funds.xlsx', [ReportPageController::class,'fundsExcel'])->name('reports.funds.xlsx');
    Route::get('reports/funds.pdf', [ReportPageController::class,'fundsPdf'])->name('reports.funds.pdf');
    Route::get('reports/campaigns', [ReportPageController::class,'campaigns'])->name('reports.campaigns');
    Route::get('reports/campaigns.xlsx', [ReportPageController::class,'campaignsExcel'])->name('reports.campaigns.xlsx');
    Route::get('reports/campaigns.pdf', [ReportPageController::class,'campaignsPdf'])->name('reports.campaigns.pdf');
    Route::get('reports/operational-ratio', [ReportPageController::class,'operationalRatio'])->name('reports.operational_ratio');
    Route::get('reports/operational-ratio.xlsx', [ReportPageController::class,'operationalRatioExcel'])->name('reports.operational_ratio.xlsx');
    Route::get('reports/operational-ratio.pdf', [ReportPageController::class,'operationalRatioPdf'])->name('reports.operational_ratio.pdf');
    Route::get('reports/budget-realization', [ReportPageController::class,'budgetRealization'])->name('reports.budget_realization');
    Route::get('reports/budget-realization.xlsx', [ReportPageController::class,'budgetRealizationExcel'])->name('reports.budget_realization.xlsx');
    Route::get('reports/budget-realization.pdf', [ReportPageController::class,'budgetRealizationPdf'])->name('reports.budget_realization.pdf');
});

// Admin-only util
Route::post('/admin/demo-reset', [DemoController::class,'reset'])->middleware(['auth','role:admin'])->name('admin.demo_reset');

Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function(){
    Route::get('users', [AdminUserController::class,'index'])->name('users.index');
    Route::get('users/create', [AdminUserController::class,'create'])->name('users.create');
    Route::post('users', [AdminUserController::class,'store'])->name('users.store');
    Route::get('users/{user}/edit', [AdminUserController::class,'edit'])->name('users.edit');
    Route::put('users/{user}', [AdminUserController::class,'update'])->name('users.update');

    Route::get('roles', [AdminRoleController::class,'index'])->name('roles.index');
    Route::post('roles/permissions', [AdminRoleController::class,'storePermission'])->name('roles.permissions.store');
    Route::put('roles/{role}/permissions', [AdminRoleController::class,'updateRolePermissions'])->name('roles.permissions.update');
});
// Public donation pages (optional subdomain)
Route::group([], function(){
    Route::get('/donasi', [\App\Http\Controllers\DonationController::class,'index'])->name('public.donation.index');
    Route::get('/donasi/program/{program}', [\App\Http\Controllers\DonationController::class,'program'])->name('public.donation.program');
    Route::post('/donasi/checkout', [\App\Http\Controllers\DonationController::class,'checkout'])->name('public.donation.checkout');
    Route::get('/donasi/status', [\App\Http\Controllers\DonationController::class,'status'])->name('public.donation.status');
    Route::get('/donasi/thanks', [\App\Http\Controllers\DonationController::class,'thanks'])->name('public.donation.thanks');
});

if ($domain = env('DONATION_DOMAIN')) {
    Route::domain($domain)->group(function(){
        Route::get('/', [\App\Http\Controllers\DonationController::class,'index'])->name('public.donation.index');
        Route::get('/donasi/program/{program}', [\App\Http\Controllers\DonationController::class,'program'])->name('public.donation.program');
        Route::post('/checkout', [\App\Http\Controllers\DonationController::class,'checkout'])->name('public.donation.checkout');
        Route::get('/status', [\App\Http\Controllers\DonationController::class,'status'])->name('public.donation.status');
        Route::get('/thanks', [\App\Http\Controllers\DonationController::class,'thanks'])->name('public.donation.thanks');
    });
}


// Mirror donor portal under /donasi/akun (same controllers)
Route::prefix('donasi/akun')->name('public.donation.account.')->group(function(){
    Route::get('google/redirect', [\App\Http\Controllers\DonorAuthController::class,'redirectToGoogle'])->name('google.redirect');
    Route::get('google/callback', [\App\Http\Controllers\DonorAuthController::class,'handleGoogleCallback'])->name('google.callback');
    Route::get('login', [\App\Http\Controllers\DonorAuthController::class,'showLogin'])->name('login');
    Route::post('request-code', [\App\Http\Controllers\DonorAuthController::class,'requestCode'])->name('request');
    Route::get('verify', [\App\Http\Controllers\DonorAuthController::class,'showVerify'])->name('verify');
    Route::post('verify', [\App\Http\Controllers\DonorAuthController::class,'verify'])->name('verify.post');
    Route::post('logout', [\App\Http\Controllers\DonorAuthController::class,'logout'])->name('logout');

    Route::get('dashboard', [\App\Http\Controllers\DonorPortalController::class,'dashboard'])->name('dashboard');
    Route::get('donations', [\App\Http\Controllers\DonorPortalController::class,'donations'])->name('donations');
    Route::post('donations/claim', [\App\Http\Controllers\DonorPortalController::class,'claim'])->name('donations.claim');
    Route::get('reports', [\App\Http\Controllers\DonorPortalController::class,'reports'])->name('reports');
});













