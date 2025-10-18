<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            if (!Schema::hasColumn('donors','login_token')) {
                $table->string('login_token', 100)->nullable();
            }
            if (!Schema::hasColumn('donors','login_token_expires_at')) {
                $table->timestamp('login_token_expires_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            if (Schema::hasColumn('donors','login_token_expires_at')) {
                $table->dropColumn('login_token_expires_at');
            }
            if (Schema::hasColumn('donors','login_token')) {
                $table->dropColumn('login_token');
            }
        });
    }
};

