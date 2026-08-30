<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_prospecting_agent_runs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->string('status')->default('draft');
            $table->boolean('approved')->default(false);
            $table->json('targeting')->nullable();
            $table->json('policy')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_prospecting_agent_targets', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('run_id');
            $table->unsignedBigInteger('prospect_id');
            $table->string('status')->default('selected');
            $table->json('research')->nullable();
            $table->json('personalization')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'run_id', 'prospect_id']);
        });
        Schema::create('crm_prospecting_agent_sequences', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('run_id');
            $table->unsignedBigInteger('target_id');
            $table->unsignedInteger('step');
            $table->string('channel');
            $table->text('content');
            $table->string('status')->default('prepared');
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_prospecting_agent_engagements', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('target_id');
            $table->string('event');
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_prospecting_agent_engagements');
        Schema::dropIfExists('crm_prospecting_agent_sequences');
        Schema::dropIfExists('crm_prospecting_agent_targets');
        Schema::dropIfExists('crm_prospecting_agent_runs');
    }
};
