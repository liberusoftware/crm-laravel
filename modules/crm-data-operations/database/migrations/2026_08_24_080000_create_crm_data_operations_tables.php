<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_data_operations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('kind', 32);
            $table->string('status', 32)->default('draft');
            $table->string('source')->nullable();
            $table->string('target')->nullable();
            $table->json('options')->nullable();
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('processed_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);
            $table->text('failure_reason')->nullable();
            $table->timestamps();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->index(['team_id', 'kind', 'status']);
        });
        Schema::create('crm_data_operation_mappings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('operation_id')->constrained('crm_data_operations')->cascadeOnDelete();
            $table->string('source_field');
            $table->string('target_field');
            $table->json('transform')->nullable();
            $table->boolean('required')->default(false);
            $table->timestamps();
            $table->unique(
                ['operation_id', 'source_field', 'target_field'],
                'crm_data_operation_mapping_fields_unique',
            );
        });
        Schema::create('crm_data_operation_rules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->string('kind', 32);
            $table->json('definition');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['team_id', 'name', 'kind']);
        });
        Schema::create('crm_data_operation_schedules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('operation_id')->constrained('crm_data_operations')->cascadeOnDelete();
            $table->string('cron');
            $table->string('timezone')->default('UTC');
            $table->boolean('active')->default(true);
            $table->timestamp('next_run_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_data_operation_exceptions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('operation_id')->constrained('crm_data_operations')->cascadeOnDelete();
            $table->unsignedInteger('row_number')->nullable();
            $table->json('payload')->nullable();
            $table->text('reason');
            $table->string('status', 24)->default('open');
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'status']);
        });
        Schema::create('crm_data_operation_duplicates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('operation_id')->constrained('crm_data_operations')->cascadeOnDelete();
            $table->unsignedBigInteger('left_record_id');
            $table->unsignedBigInteger('right_record_id');
            $table->decimal('confidence', 5, 4);
            $table->string('status', 24)->default('pending');
            $table->timestamps();
            $table->unique(['operation_id', 'left_record_id', 'right_record_id']);
        });
        Schema::create('crm_data_operation_audits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('operation_id')->constrained('crm_data_operations')->cascadeOnDelete();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('event', 64);
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_data_operation_audits', 'crm_data_operation_duplicates', 'crm_data_operation_exceptions', 'crm_data_operation_schedules', 'crm_data_operation_rules', 'crm_data_operation_mappings', 'crm_data_operations'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
