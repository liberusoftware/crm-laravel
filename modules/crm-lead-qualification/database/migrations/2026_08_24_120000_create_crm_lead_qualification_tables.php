<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lead_qualification_frameworks', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('name', 160);
            $table->string('status', 24)->default('draft');
            $table->unsignedInteger('mql_threshold')->default(50);
            $table->unsignedInteger('pql_threshold')->default(65);
            $table->unsignedInteger('sql_threshold')->default(80);
            $table->unsignedInteger('service_qualified_threshold')->default(90);
            $table->json('rules')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'name']);
        });

        Schema::create('crm_lead_qualifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('subject_type', 160);
            $table->unsignedBigInteger('subject_id');
            $table->foreignId('framework_id')->nullable()->constrained('crm_lead_qualification_frameworks')->nullOnDelete();
            $table->string('lifecycle_stage', 48)->default('subscriber');
            $table->unsignedInteger('fit_score')->default(0);
            $table->unsignedInteger('engagement_score')->default(0);
            $table->unsignedInteger('total_score')->default(0);
            $table->string('qualification_status', 32)->default('unqualified');
            $table->string('disqualification_reason', 255)->nullable();
            $table->timestamp('nurture_until')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'subject_type', 'subject_id']);
            $table->index(
                ['team_id', 'lifecycle_stage', 'qualification_status'],
                'crm_lead_qualification_stage_status_index',
            );
        });

        Schema::create('crm_lead_qualification_stage_history', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('qualification_id')->constrained('crm_lead_qualifications')->cascadeOnDelete();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('from_stage', 48)->nullable();
            $table->string('to_stage', 48);
            $table->string('reason', 255)->nullable();
            $table->timestamps();
            $table->index(['team_id', 'qualification_id'], 'crm_lead_qualification_history_index');
        });

        Schema::create('crm_lead_qualification_nurtures', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('qualification_id')->constrained('crm_lead_qualifications')->cascadeOnDelete();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('status', 24)->default('active');
            $table->string('sequence', 160)->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'status', 'ends_at']);
        });

        Schema::create('crm_lead_qualification_audits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('qualification_id')->nullable()->constrained('crm_lead_qualifications')->nullOnDelete();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('event', 80);
            $table->json('details')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_qualification_audits');
        Schema::dropIfExists('crm_lead_qualification_nurtures');
        Schema::dropIfExists('crm_lead_qualification_stage_history');
        Schema::dropIfExists('crm_lead_qualifications');
        Schema::dropIfExists('crm_lead_qualification_frameworks');
    }
};
