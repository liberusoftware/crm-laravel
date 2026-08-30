<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_engagement_sequences', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->string('timezone')->default('UTC');
            $t->json('throttle')->nullable();
            $t->json('stop_rules')->nullable();
            $t->json('experiment')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
        Schema::create('crm_engagement_steps', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('sequence_id')->constrained('crm_engagement_sequences')->cascadeOnDelete();
            $t->unsignedInteger('position');
            $t->string('channel');
            $t->unsignedInteger('delay_minutes')->default(0);
            $t->text('template')->nullable();
            $t->json('snippet')->nullable();
            $t->timestamps();
            $t->unique(['sequence_id', 'position']);
        });
        Schema::create('crm_engagement_enrollments', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('sequence_id')->constrained('crm_engagement_sequences')->cascadeOnDelete();
            $t->unsignedBigInteger('contact_id');
            $t->string('status')->default('active');
            $t->unsignedInteger('current_step')->default(0);
            $t->unsignedInteger('reentry_count')->default(0);
            $t->timestamp('next_run_at')->nullable();
            $t->timestamps();
            $t->unique(['sequence_id', 'contact_id']);
        });
        Schema::create('crm_engagement_tasks', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('enrollment_id')->constrained('crm_engagement_enrollments')->cascadeOnDelete();
            $t->string('channel');
            $t->string('status')->default('queued');
            $t->timestamp('due_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
        });
        Schema::create('crm_engagement_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->unsignedBigInteger('contact_id');
            $t->string('event');
            $t->json('payload')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_engagement_events', 'crm_engagement_tasks', 'crm_engagement_enrollments', 'crm_engagement_steps', 'crm_engagement_sequences'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
