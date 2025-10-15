<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('disbursements')) {
            Schema::create('disbursements', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->foreignId('program_id')->constrained('programs');
                $table->foreignId('beneficiary_id')->constrained('beneficiaries');
                $table->foreignId('requested_by')->constrained('users');
                $table->bigInteger('amount');
                $table->string('method_preference')->nullable(); // cash|transfer|ewallet
                $table->text('purpose')->nullable();
                $table->string('status')->default('draft');
                $table->timestamp('submitted_at')->nullable();
                $table->foreignId('assessed_by')->nullable()->constrained('users');
                $table->text('assessed_note')->nullable();
                $table->timestamp('assessed_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('disbursement_approvals')) {
            Schema::create('disbursement_approvals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('disbursement_id')->constrained('disbursements')->cascadeOnDelete();
                $table->integer('level'); // 1=program, 2=finance, 3=approver
                $table->foreignId('approver_id')->constrained('users');
                $table->string('status')->default('pending');
                $table->text('note')->nullable();
                $table->timestamp('decided_at')->nullable();
                $table->timestamps();
                $table->unique(['disbursement_id','level']);
            });
        }

        if (!Schema::hasTable('disbursement_payments')) {
            Schema::create('disbursement_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('disbursement_id')->constrained('disbursements')->cascadeOnDelete();
                $table->string('channel'); // cash|transfer|ewallet
                $table->foreignId('account_id')->nullable()->constrained('accounts');
                $table->bigInteger('amount');
                $table->timestamp('paid_at')->nullable();
                $table->string('recipient_name')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('account_no')->nullable();
                $table->string('ewallet_id')->nullable();
                $table->string('ref_no')->nullable();
                $table->string('receipt_url')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('disbursement_payments');
        Schema::dropIfExists('disbursement_approvals');
        Schema::dropIfExists('disbursements');
    }
};

