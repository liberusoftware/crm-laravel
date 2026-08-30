<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_referral_programs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->string('code_prefix')->default('REF');
            $table->decimal('reward_amount', 12, 2)->default(0);
            $table->string('reward_currency', 3)->default('USD');
            $table->boolean('active')->default(true);
            $table->json('rules')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_referrals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('advocate_id')->nullable();
            $table->string('code')->unique();
            $table->string('prospect_email');
            $table->string('prospect_name')->nullable();
            $table->string('status')->default('pending');
            $table->string('fraud_status')->default('clear');
            $table->string('source')->nullable();
            $table->timestamp('attributed_at')->nullable();
            $table->timestamp('qualified_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'prospect_email']);
        });
        Schema::create('crm_referral_rewards', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('referral_id');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->string('status')->default('pending');
            $table->string('idempotency_key')->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_referral_rewards');
        Schema::dropIfExists('crm_referrals');
        Schema::dropIfExists('crm_referral_programs');
    }
};
