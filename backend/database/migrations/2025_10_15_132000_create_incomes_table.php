<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no')->unique();
            $table->date('tanggal');
            $table->string('channel'); // transfer|qris|va|tunai|gateway
            $table->bigInteger('amount');
            $table->foreignId('donor_id')->nullable()->constrained('donors');
            $table->foreignId('program_id')->nullable()->constrained('programs'); // null => general fund
            $table->string('status')->default('recorded'); // recorded|matched
            $table->string('ref_no')->nullable(); // ref bank / gateway id
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['tanggal','channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};

