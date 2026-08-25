<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_revenue_assets', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('name');
            $t->string('status')->default('active');
            $t->string('lifecycle_action')->nullable();
            $t->date('renewal_date')->nullable();
            $t->json('entitlements')->nullable();
            $t->json('usage_signals')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status', 'renewal_date']);
        });
        Schema::create('crm_revenue_orders', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->unsignedBigInteger('opportunity_id')->nullable();
            $t->string('status')->default('pending');
            $t->decimal('value', 20, 6)->default(0);
            $t->string('billing_reference')->nullable();
            $t->text('failure_reason')->nullable();
            $t->timestamps();
        });
        Schema::create('crm_revenue_fallout', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('subject_type');
            $t->unsignedBigInteger('subject_id');
            $t->string('reason');
            $t->string('status')->default('open');
            $t->json('details')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        foreach (['crm_revenue_fallout', 'crm_revenue_orders', 'crm_revenue_assets'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
