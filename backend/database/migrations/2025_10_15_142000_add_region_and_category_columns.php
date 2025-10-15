<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            if (!Schema::hasColumn('beneficiaries','region')) {
                $table->string('region')->nullable()->after('address');
            }
        });

        Schema::table('transaksi', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi','category')) {
                $table->string('category')->nullable()->after('program_id'); // operational|program
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi','category')) {
                $table->dropColumn('category');
            }
        });
        Schema::table('beneficiaries', function (Blueprint $table) {
            if (Schema::hasColumn('beneficiaries','region')) {
                $table->dropColumn('region');
            }
        });
    }
};

