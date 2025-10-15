<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_mutations', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('description')->nullable();
            $table->bigInteger('amount');
            $table->string('channel')->nullable();
            $table->string('ref_no')->nullable();
            $table->foreignId('matched_income_id')->nullable()->constrained('incomes');
            $table->foreignId('matched_transaction_id')->nullable()->constrained('transaksi');
            $table->timestamps();
            $table->index(['tanggal','amount']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_mutations');
    }
};
