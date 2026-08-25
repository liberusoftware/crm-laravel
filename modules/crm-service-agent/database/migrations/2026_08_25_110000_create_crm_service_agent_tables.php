<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_service_agent_cases', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('subject');
            $t->text('input');
            $t->string('status')->default('new');
            $t->string('classification')->nullable();
            $t->decimal('confidence', 5, 4)->nullable();
            $t->text('response_draft')->nullable();
            $t->json('resolution_plan')->nullable();
            $t->unsignedInteger('escalation_level')->default(0);
            $t->string('idempotency_key')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'idempotency_key']);
            $t->index(['team_id', 'status']);
        });
        Schema::create('crm_service_agent_knowledge', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('title');
            $t->longText('content');
            $t->string('source')->nullable();
            $t->json('tags')->nullable();
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->index(['team_id', 'active']);
        });
        Schema::create('crm_service_agent_tool_runs', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('case_id')->constrained('crm_service_agent_cases')->cascadeOnDelete();
            $t->string('tool');
            $t->string('status')->default('pending');
            $t->json('input')->nullable();
            $t->json('output')->nullable();
            $t->text('error')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status']);
        });
        Schema::create('crm_service_agent_reviews', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('case_id')->constrained('crm_service_agent_cases')->cascadeOnDelete();
            $t->unsignedBigInteger('reviewer_id');
            $t->unsignedTinyInteger('score')->nullable();
            $t->text('feedback')->nullable();
            $t->string('status')->default('pending');
            $t->timestamps();
            $t->unique(['case_id', 'reviewer_id']);
        });
        Schema::create('crm_service_agent_audits', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('event');
            $t->json('details')->nullable();
            $t->string('request_id')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_service_agent_audits', 'crm_service_agent_reviews', 'crm_service_agent_tool_runs', 'crm_service_agent_knowledge', 'crm_service_agent_cases'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
