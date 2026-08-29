<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_bpm_processes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('owner_id')->nullable()->index();
            $table->string('key');
            $table->string('name');
            $table->string('status')->default('draft');
            $table->unsignedInteger('version')->default(1);
            $table->json('definition');
            $table->timestamps();
            $table->unique(['team_id', 'key', 'version']);
        });

        Schema::create('crm_bpm_runs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('process_id')->constrained('crm_bpm_processes')->cascadeOnDelete();
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->string('subject_type')->nullable();
            $table->string('subject_key')->nullable();
            $table->string('status')->default('running');
            $table->string('current_step')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_bpm_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('run_id')->constrained('crm_bpm_runs')->cascadeOnDelete();
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->string('type');
            $table->string('status')->default('recorded');
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_bpm_events');
        Schema::dropIfExists('crm_bpm_runs');
        Schema::dropIfExists('crm_bpm_processes');
    }
};
