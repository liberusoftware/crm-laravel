<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_usage_wallets', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('currency', 3)->default('USD');
            $table->decimal('balance', 18, 6)->default(0);
            $table->decimal('threshold', 18, 6)->default(0);
            $table->decimal('reload_amount', 18, 6)->default(0);
            $table->string('status')->default('active');
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->unique('team_id');
        });
        Schema::create('crm_usage_imports', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('provider');
            $table->string('external_id');
            $table->decimal('amount', 18, 6);
            $table->string('currency', 3);
            $table->string('status')->default('pending');
            $table->text('failure_reason')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'provider', 'external_id']);
        });
        Schema::create('crm_usage_pricing_rules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('provider');
            $table->decimal('markup_percent', 8, 4)->default(0);
            $table->decimal('fixed_fee', 18, 6)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('crm_usage_charges', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('usage_import_id')->nullable();
            $table->unsignedBigInteger('wallet_id')->nullable();
            $table->string('client_reference')->nullable();
            $table->decimal('cost', 18, 6);
            $table->decimal('charge', 18, 6);
            $table->string('currency', 3);
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->unique(['team_id', 'usage_import_id']);
        });
        Schema::create('crm_usage_exceptions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('usage_import_id')->nullable();
            $table->string('kind');
            $table->text('message');
            $table->string('status')->default('open');
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_usage_reconciliations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->date('period');
            $table->decimal('provider_total', 18, 6)->default(0);
            $table->decimal('imported_total', 18, 6)->default(0);
            $table->decimal('variance', 18, 6)->default(0);
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->unique(['team_id', 'period']);
        });
        Schema::create('crm_usage_handoffs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('target');
            $table->string('period');
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->string('idempotency_key');
            $table->timestamps();
            $table->unique(['team_id', 'target', 'period']);
        });
        Schema::create('crm_usage_audits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('event');
            $table->json('details')->nullable();
            $table->string('request_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_usage_audits', 'crm_usage_handoffs', 'crm_usage_reconciliations', 'crm_usage_exceptions', 'crm_usage_charges', 'crm_usage_pricing_rules', 'crm_usage_imports', 'crm_usage_wallets'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
