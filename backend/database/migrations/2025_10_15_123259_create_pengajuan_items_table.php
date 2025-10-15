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
        Schema::create('pengajuan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuans')->cascadeOnDelete();
            $table->string('account_code');
            $table->string('description');
            $table->decimal('qty', 18, 2)->default(1);
            $table->bigInteger('unit_price')->default(0);
            $table->bigInteger('subtotal')->default(0);
            $table->foreignId('anggaran_item_id')->nullable()->constrained('anggaran_items');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_items');
    }
};
