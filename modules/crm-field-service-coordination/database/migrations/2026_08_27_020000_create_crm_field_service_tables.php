<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_field_service_work_types', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('name');
            $table->string('code');
            $table->unsignedInteger('default_duration')->default(60);
            $table->json('requirements')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['team_id', 'code']);
        });
        Schema::create('crm_field_service_appointments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('work_type_id')->constrained('crm_field_service_work_types');
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->string('status')->default('scheduled');
            $table->string('subject');
            $table->string('location')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->text('notes')->nullable();
            $table->json('dispatch')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'starts_at']);
        });
        Schema::create('crm_field_service_assets', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('external_key')->nullable();
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'external_key']);
        });
        Schema::create('crm_field_service_history', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('appointment_id')->constrained('crm_field_service_appointments');
            $table->foreignId('asset_id')->nullable()->constrained('crm_field_service_assets');
            $table->unsignedBigInteger('actor_id');
            $table->string('event');
            $table->text('details')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_field_service_handoffs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('appointment_id')->constrained('crm_field_service_appointments');
            $table->unsignedBigInteger('actor_id');
            $table->string('target')->default('maintenance');
            $table->string('status')->default('pending');
            $table->json('payload');
            $table->timestamp('handed_off_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_field_service_handoffs');
        Schema::dropIfExists('crm_field_service_history');
        Schema::dropIfExists('crm_field_service_assets');
        Schema::dropIfExists('crm_field_service_appointments');
        Schema::dropIfExists('crm_field_service_work_types');
    }
};
