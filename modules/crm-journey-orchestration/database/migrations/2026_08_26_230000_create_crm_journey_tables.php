<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_journeys', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('slug');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->unsignedInteger('version')->default(1);
            $t->string('trigger_type')->default('event');
            $t->json('definition');
            $t->json('controls')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'slug']);
            $t->index(['team_id', 'status']);
        });
        Schema::create('crm_journey_runs', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('journey_id')->constrained('crm_journeys')->cascadeOnDelete();
            $t->unsignedBigInteger('subject_id');
            $t->string('status')->default('active');
            $t->string('current_step')->nullable();
            $t->timestamp('next_at')->nullable();
            $t->timestamp('stopped_at')->nullable();
            $t->string('stop_reason')->nullable();
            $t->json('context')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'journey_id', 'subject_id']);
            $t->index(['team_id', 'status', 'next_at']);
        });
        Schema::create('crm_journey_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('journey_id')->constrained('crm_journeys')->cascadeOnDelete();
            $t->foreignId('run_id')->nullable()->constrained('crm_journey_runs')->nullOnDelete();
            $t->string('kind');
            $t->string('status')->default('recorded');
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'journey_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_journey_events');
        Schema::dropIfExists('crm_journey_runs');
        Schema::dropIfExists('crm_journeys');
    }
};
