<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_activities', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable()->index();
            $table->string('kind', 32);
            $table->string('status', 24)->default('planned');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('subject_type', 120)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('recurrence', 24)->nullable();
            $table->timestamp('recurrence_until')->nullable();
            $table->timestamp('reminder_at')->nullable();
            $table->string('queue', 120)->nullable();
            $table->string('outcome', 120)->nullable();
            $table->text('outcome_notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'kind', 'status']);
            $table->index(['team_id', 'subject_type', 'subject_id']);
            $table->index(['team_id', 'due_at']);
        });
        Schema::create('crm_activity_goals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name');
            $table->string('kind', 40);
            $table->unsignedInteger('target')->default(0);
            $table->unsignedInteger('progress')->default(0);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status', 24)->default('active');
            $table->json('criteria')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'status', 'ends_at']);
        });
        Schema::create('crm_activity_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('activity_id')->constrained('crm_activities')->cascadeOnDelete();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('event', 64);
            $table->json('details')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activity_events');
        Schema::dropIfExists('crm_activity_goals');
        Schema::dropIfExists('crm_activities');
    }
};
