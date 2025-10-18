<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            if (!Schema::hasColumn('beneficiaries','national_id')) $table->string('national_id', 32)->nullable()->after('guardian_name');
            if (!Schema::hasColumn('beneficiaries','family_card_no')) $table->string('family_card_no', 32)->nullable()->after('national_id');
            if (!Schema::hasColumn('beneficiaries','gender')) $table->string('gender', 10)->nullable()->after('family_card_no');
            if (!Schema::hasColumn('beneficiaries','education')) $table->string('education', 100)->nullable()->after('gender');
            if (!Schema::hasColumn('beneficiaries','occupation')) $table->string('occupation', 100)->nullable()->after('education');
            if (!Schema::hasColumn('beneficiaries','guardian_phone')) $table->string('guardian_phone', 30)->nullable()->after('phone');
            if (!Schema::hasColumn('beneficiaries','city')) $table->string('city', 100)->nullable()->after('address');
            if (!Schema::hasColumn('beneficiaries','province')) $table->string('province', 100)->nullable()->after('city');
            if (!Schema::hasColumn('beneficiaries','postal_code')) $table->string('postal_code', 10)->nullable()->after('province');
        });
    }

    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            foreach (['national_id','family_card_no','gender','education','occupation','guardian_phone','city','province','postal_code'] as $col) {
                if (Schema::hasColumn('beneficiaries', $col)) $table->dropColumn($col);
            }
        });
    }
};

