<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('name');
            $table->string('slug');
            $table->string('format')->default('physical');
            $table->string('status')->default('draft');
            $table->unsignedInteger('capacity')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('location')->nullable();
            $table->string('recording_url')->nullable();
            $table->json('provider')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'slug']);
        });
        Schema::create('crm_event_registrations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('event_id')->constrained('crm_events');
            $table->unsignedBigInteger('attendee_id')->nullable();
            $table->string('email');
            $table->string('status')->default('registered');
            $table->string('ticket')->default('general');
            $table->timestamp('checked_in_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'email']);
        });
        Schema::create('crm_event_sessions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('event_id')->constrained('crm_events');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->json('speakers')->nullable();
            $table->string('recording_url')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_event_follow_ups', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('event_id')->constrained('crm_events');
            $table->unsignedBigInteger('actor_id');
            $table->string('kind');
            $table->string('status')->default('queued');
            $table->json('payload');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_event_follow_ups');
        Schema::dropIfExists('crm_event_sessions');
        Schema::dropIfExists('crm_event_registrations');
        Schema::dropIfExists('crm_events');
    }
};
