<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_quotas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('territory')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('target', 14, 2);
            $table->decimal('attained', 14, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('draft');
            $table->json('ramp')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'user_id', 'period_start']);
        });
        Schema::create('crm_commission_plans', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->decimal('rate', 8, 4);
            $table->json('accelerators')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('crm_commission_credits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('quota_id')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->decimal('amount', 14, 2);
            $table->decimal('commission', 14, 2);
            $table->string('status')->default('pending');
            $table->string('idempotency_key')->unique();
            $table->timestamps();
            $table->index(['team_id', 'user_id']);
        });
        Schema::create('crm_commission_disputes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('credit_id');
            $table->unsignedBigInteger('opened_by');
            $table->text('reason');
            $table->string('status')->default('open');
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_commission_exports', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('format')->default('csv');
            $table->string('status')->default('pending');
            $table->string('period');
            $table->text('location')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_commission_exports');
        Schema::dropIfExists('crm_commission_disputes');
        Schema::dropIfExists('crm_commission_credits');
        Schema::dropIfExists('crm_commission_plans');
        Schema::dropIfExists('crm_quotas');
    }
};
