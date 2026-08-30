<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_telephony_numbers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('number', 32);
            $table->string('label')->nullable();
            $table->string('provider')->default('twilio');
            $table->string('status')->default('active');
            $table->boolean('caller_id_enabled')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'number']);
            $table->index(['team_id', 'status']);
        });
        Schema::create('crm_telephony_queues', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('name');
            $table->string('strategy')->default('ring_all');
            $table->unsignedInteger('max_wait_seconds')->default(300);
            $table->json('members')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['team_id', 'name']);
        });
        Schema::create('crm_telephony_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->unique();
            $table->string('provider')->default('twilio');
            $table->json('business_hours')->nullable();
            $table->json('ivr')->nullable();
            $table->json('skills')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });
        Schema::create('crm_telephony_calls', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('external_id')->nullable();
            $table->foreignId('number_id')->nullable()->constrained('crm_telephony_numbers')->nullOnDelete();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->string('from_number', 32);
            $table->string('to_number', 32);
            $table->string('direction')->default('inbound');
            $table->string('status')->default('completed');
            $table->string('disposition')->nullable();
            $table->string('recording_url')->nullable();
            $table->string('voicemail_url')->nullable();
            $table->string('transfer_to')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('idempotency_key')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'idempotency_key']);
            $table->index(['team_id', 'created_at']);
        });
        Schema::create('crm_telephony_audits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('event');
            $table->json('details')->nullable();
            $table->string('request_id')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_telephony_audits');
        Schema::dropIfExists('crm_telephony_calls');
        Schema::dropIfExists('crm_telephony_settings');
        Schema::dropIfExists('crm_telephony_queues');
        Schema::dropIfExists('crm_telephony_numbers');
    }
};
