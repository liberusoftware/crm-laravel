<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_performance_goals', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->unsignedBigInteger('owner_id');
            $t->string('scope')->default('individual');
            $t->string('name');
            $t->string('status')->default('active');
            $t->decimal('target', 14, 2);
            $t->decimal('actual', 14, 2)->default(0);
            $t->date('starts_on');
            $t->date('ends_on')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'scope', 'status', 'owner_id']);
        });
        Schema::create('crm_performance_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('goal_id')->constrained('crm_performance_goals')->cascadeOnDelete();
            $t->string('kind');
            $t->string('status')->default('recorded');
            $t->decimal('value', 14, 2)->nullable();
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->text('notes')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'goal_id', 'kind']);
        });
        Schema::create('crm_performance_reviews', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->unsignedBigInteger('subject_id');
            $t->unsignedBigInteger('reviewer_id');
            $t->string('period');
            $t->string('status')->default('draft');
            $t->unsignedInteger('score')->nullable();
            $t->text('summary')->nullable();
            $t->json('coaching_plan')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'subject_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_performance_reviews');
        Schema::dropIfExists('crm_performance_events');
        Schema::dropIfExists('crm_performance_goals');
    }
};
