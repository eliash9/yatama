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
        Schema::create('lampirans', function (Blueprint $table) {
            $table->id();
            $table->string('ref_type');
            $table->unsignedBigInteger('ref_id');
            $table->string('filename');
            $table->string('mime');
            $table->unsignedBigInteger('size');
            $table->string('url');
            $table->foreignId('uploader_id')->constrained('users');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampirans');
    }
};
