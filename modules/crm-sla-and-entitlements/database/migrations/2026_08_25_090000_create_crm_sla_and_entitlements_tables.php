<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_sla_calendars', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->string('timezone')->default('UTC');
            $t->json('weekly_schedule')->nullable();
            $t->json('holidays')->nullable();
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
        Schema::create('crm_sla_contracts', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->foreignId('calendar_id')->nullable()->constrained('crm_sla_calendars')->nullOnDelete();
            $t->date('starts_on')->nullable();
            $t->date('ends_on')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status']);
        });
        Schema::create('crm_sla_entitlements', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('contract_id')->constrained('crm_sla_contracts')->cascadeOnDelete();
            $t->string('name');
            $t->string('priority')->default('normal');
            $t->unsignedInteger('response_minutes');
            $t->unsignedInteger('resolution_minutes');
            $t->unsignedInteger('warning_minutes')->default(30);
            $t->boolean('active')->default(true);
            $t->json('coverage')->nullable();
            $t->timestamps();
            $t->unique(['contract_id', 'name']);
        });
        Schema::create('crm_sla_cases', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('contract_id')->nullable()->constrained('crm_sla_contracts')->nullOnDelete();
            $t->foreignId('entitlement_id')->nullable()->constrained('crm_sla_entitlements')->nullOnDelete();
            $t->string('subject');
            $t->string('status')->default('open');
            $t->timestamp('opened_at');
            $t->timestamp('response_due_at')->nullable();
            $t->timestamp('resolution_due_at')->nullable();
            $t->timestamp('responded_at')->nullable();
            $t->timestamp('resolved_at')->nullable();
            $t->unsignedInteger('paused_minutes')->default(0);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status']);
        });
        Schema::create('crm_sla_case_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('case_id')->constrained('crm_sla_cases')->cascadeOnDelete();
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('type');
            $t->json('payload')->nullable();
            $t->timestamp('occurred_at');
            $t->string('request_id')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'type']);
        });
        Schema::create('crm_sla_escalations', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('case_id')->constrained('crm_sla_cases')->cascadeOnDelete();
            $t->unsignedInteger('level')->default(1);
            $t->string('status')->default('pending');
            $t->string('target')->nullable();
            $t->timestamp('triggered_at')->nullable();
            $t->timestamp('resolved_at')->nullable();
            $t->timestamps();
            $t->unique(['case_id', 'level']);
        });
        Schema::create('crm_sla_exceptions', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('case_id')->constrained('crm_sla_cases')->cascadeOnDelete();
            $t->string('reason');
            $t->string('status')->default('pending');
            $t->unsignedBigInteger('requested_by');
            $t->unsignedBigInteger('approved_by')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_sla_exceptions', 'crm_sla_escalations', 'crm_sla_case_events', 'crm_sla_cases', 'crm_sla_entitlements', 'crm_sla_contracts', 'crm_sla_calendars'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
