<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_saas_plans', function (Blueprint $t): void {
            $t->id();
            $t->string('key')->unique();
            $t->string('name');
            $t->decimal('price', 20, 6)->default(0);
            $t->string('currency', 3)->default('USD');
            $t->json('entitlements')->nullable();
            $t->json('limits')->nullable();
            $t->unsignedInteger('trial_days')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });
        Schema::create('crm_saas_subscriptions', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->unique();
            $t->foreignId('plan_id')->constrained('crm_saas_plans');
            $t->string('status')->default('trialing');
            $t->timestamp('trial_ends_at')->nullable();
            $t->timestamp('current_period_ends_at')->nullable();
            $t->string('billing_provider')->nullable();
            $t->string('billing_reference')->nullable();
            $t->timestamp('cancelled_at')->nullable();
            $t->timestamps();
        });
        Schema::create('crm_saas_usage', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('feature');
            $t->unsignedInteger('quantity')->default(0);
            $t->timestamp('period_start');
            $t->timestamp('period_end');
            $t->timestamps();
            $t->unique(['team_id', 'feature', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        foreach (['crm_saas_usage', 'crm_saas_subscriptions', 'crm_saas_plans'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
