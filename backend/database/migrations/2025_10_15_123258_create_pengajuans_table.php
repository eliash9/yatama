<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('periode_id')->constrained('periodes');
            $table->foreignId('pemohon_id')->constrained('users');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->bigInteger('total_diminta')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
