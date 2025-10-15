<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns to transaksi if missing
        Schema::table('transaksi', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi', 'account_id')) {
                $table->foreignId('account_id')->nullable()->after('akun_kas')->constrained('accounts');
            }
            if (!Schema::hasColumn('transaksi', 'program_id')) {
                $table->foreignId('program_id')->nullable()->after('ref_id')->constrained('programs');
            }
            if (!Schema::hasColumn('transaksi', 'reconciled_at')) {
                $table->timestamp('reconciled_at')->nullable()->after('memo');
            }
        });

        // Add column to bank_mutations if missing
        Schema::table('bank_mutations', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_mutations', 'matched_transaction_id')) {
                $table->foreignId('matched_transaction_id')->nullable()->after('matched_income_id')->constrained('transaksi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bank_mutations', function (Blueprint $table) {
            if (Schema::hasColumn('bank_mutations', 'matched_transaction_id')) {
                $table->dropConstrainedForeignId('matched_transaction_id');
            }
        });

        Schema::table('transaksi', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi', 'reconciled_at')) {
                $table->dropColumn('reconciled_at');
            }
            if (Schema::hasColumn('transaksi', 'program_id')) {
                $table->dropConstrainedForeignId('program_id');
            }
            if (Schema::hasColumn('transaksi', 'account_id')) {
                $table->dropConstrainedForeignId('account_id');
            }
        });
    }
};

