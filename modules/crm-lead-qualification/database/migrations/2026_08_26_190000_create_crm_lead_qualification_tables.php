<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lead_qualification_leads', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('external_key');
            $t->string('stage')->default('new');
            $t->string('qualification')->default('unqualified');
            $t->unsignedInteger('fit_score')->default(0);
            $t->unsignedInteger('engagement_score')->default(0);
            $t->boolean('nurture')->default(false);
            $t->text('disqualification_reason')->nullable();
            $t->string('conversion_reference')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'external_key']);
            $t->index(['team_id', 'stage', 'qualification']);
        });
        Schema::create('crm_lead_qualification_rules', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->string('qualification');
            $t->unsignedInteger('fit_threshold')->default(0);
            $t->unsignedInteger('engagement_threshold')->default(0);
            $t->json('framework')->nullable();
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
        Schema::create('crm_lead_qualification_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('lead_id')->constrained('crm_lead_qualification_leads')->cascadeOnDelete();
            $t->string('kind');
            $t->string('from_value')->nullable();
            $t->string('to_value')->nullable();
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->text('reason')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'lead_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_qualification_events');
        Schema::dropIfExists('crm_lead_qualification_rules');
        Schema::dropIfExists('crm_lead_qualification_leads');
    }
};
