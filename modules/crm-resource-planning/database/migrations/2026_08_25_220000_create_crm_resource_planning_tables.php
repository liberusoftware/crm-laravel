<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_resource_skills', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('proficiency')->default(1);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'name']);
        });
        Schema::create('crm_resource_capacity', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('resource_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('available_hours', 10, 2);
            $table->decimal('allocated_hours', 10, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(
                ['team_id', 'resource_id', 'period_start', 'period_end'],
                'crm_resource_capacity_period_unique',
            );
        });
        Schema::create('crm_resource_bookings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('resource_id');
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->decimal('hours', 10, 2);
            $table->string('status')->default('tentative');
            $table->decimal('rate', 12, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(
                ['team_id', 'resource_id', 'starts_at', 'ends_at'],
                'crm_resource_booking_window_index',
            );
        });
        Schema::create('crm_resource_rates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->unsignedBigInteger('skill_id')->nullable();
            $table->decimal('hourly_rate', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_resource_forecasts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('demand_hours', 10, 2);
            $table->decimal('available_hours', 10, 2);
            $table->json('assumptions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_resource_forecasts');
        Schema::dropIfExists('crm_resource_rates');
        Schema::dropIfExists('crm_resource_bookings');
        Schema::dropIfExists('crm_resource_capacity');
        Schema::dropIfExists('crm_resource_skills');
    }
};
