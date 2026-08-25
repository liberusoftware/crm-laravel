<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_scheduling_links', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('slug');
            $t->string('name');
            $t->string('kind')->default('personal');
            $t->unsignedInteger('duration_minutes')->default(30);
            $t->unsignedInteger('buffer_before')->default(0);
            $t->unsignedInteger('buffer_after')->default(0);
            $t->unsignedInteger('minimum_notice_minutes')->default(60);
            $t->json('availability')->nullable();
            $t->json('questions')->nullable();
            $t->json('reminders')->nullable();
            $t->json('routing')->nullable();
            $t->string('calendar_adapter')->nullable();
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->unique(['team_id', 'slug']);
        });
        Schema::create('crm_scheduling_bookings', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('link_id')->constrained('crm_scheduling_links')->cascadeOnDelete();
            $t->unsignedBigInteger('invitee_id')->nullable();
            $t->string('invitee_name');
            $t->string('invitee_email');
            $t->timestamp('starts_at');
            $t->timestamp('ends_at');
            $t->string('status')->default('confirmed');
            $t->json('answers')->nullable();
            $t->string('external_event_id')->nullable();
            $t->string('idempotency_key')->nullable();
            $t->text('cancel_reason')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'idempotency_key']);
            $t->index(['team_id', 'starts_at', 'status']);
        });
        Schema::create('crm_scheduling_audits', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('event');
            $t->json('details')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_scheduling_audits', 'crm_scheduling_bookings', 'crm_scheduling_links'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
