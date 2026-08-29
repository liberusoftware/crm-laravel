<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_web_intent_visits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('visitor_key', 128);
            $table->string('session_key', 128)->nullable();
            $table->string('ip_hash', 128)->nullable();
            $table->string('user_agent_hash', 128)->nullable();
            $table->string('landing_url', 2048)->nullable();
            $table->string('referrer', 2048)->nullable();
            $table->string('consent_status', 32)->default('unknown');
            $table->unsignedInteger('score')->default(0);
            $table->string('intent_level', 32)->default('unknown');
            $table->string('status', 32)->default('active');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'visitor_key']);
            $table->index(['team_id', 'intent_level', 'status']);
        });

        Schema::create('crm_web_intent_engagements', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('visit_id')->index();
            $table->string('visitor_key', 128);
            $table->string('event_type', 64);
            $table->string('page_url', 2048)->nullable();
            $table->string('content_type', 100)->nullable();
            $table->string('content_id', 190)->nullable();
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('dedupe_key', 190)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->unique(['team_id', 'dedupe_key']);
        });

        Schema::create('crm_web_intent_identifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('visitor_key', 128);
            $table->string('adapter', 100);
            $table->string('account_name')->nullable();
            $table->string('account_domain')->nullable();
            $table->unsignedTinyInteger('confidence')->default(0);
            $table->string('status', 32)->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'visitor_key', 'adapter'], 'crm_web_intent_identification_unique');
        });

        Schema::create('crm_web_intent_signals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('visit_id')->nullable()->index();
            $table->string('visitor_key', 128);
            $table->string('signal', 100);
            $table->unsignedInteger('points');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['team_id', 'visitor_key', 'signal']);
        });

        Schema::create('crm_web_intent_alerts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('visitor_key', 128);
            $table->unsignedBigInteger('visit_id')->nullable()->index();
            $table->string('severity', 32)->default('normal');
            $table->string('title', 255);
            $table->text('details')->nullable();
            $table->string('status', 32)->default('open');
            $table->timestamp('triggered_at');
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'status', 'severity']);
        });

        Schema::create('crm_web_intent_consents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('visitor_key', 128);
            $table->string('purpose', 100);
            $table->string('status', 32)->default('unknown');
            $table->string('policy_version', 64)->nullable();
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'visitor_key', 'purpose']);
        });

        Schema::create('crm_web_intent_conversions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('visitor_key', 128);
            $table->unsignedBigInteger('visit_id')->nullable()->index();
            $table->string('target_type', 100);
            $table->unsignedBigInteger('target_id');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('status', 32)->default('completed');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(
                ['team_id', 'visitor_key', 'target_type', 'target_id'],
                'crm_web_intent_conversion_target_unique',
            );
        });

        Schema::create('crm_web_intent_audits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->string('event', 100);
            $table->json('details')->nullable();
            $table->string('request_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_web_intent_audits', 'crm_web_intent_conversions', 'crm_web_intent_consents', 'crm_web_intent_alerts', 'crm_web_intent_signals', 'crm_web_intent_identifications', 'crm_web_intent_engagements', 'crm_web_intent_visits'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
