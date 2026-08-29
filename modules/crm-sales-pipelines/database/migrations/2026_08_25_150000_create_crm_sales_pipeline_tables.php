<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_sales_pipelines', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
        Schema::create('crm_sales_stages', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('pipeline_id')->constrained('crm_sales_pipelines')->cascadeOnDelete();
            $t->string('name');
            $t->unsignedInteger('position');
            $t->decimal('probability', 5, 2)->default(0);
            $t->unsignedInteger('rotting_days')->nullable();
            $t->timestamps();
            $t->unique(['pipeline_id', 'name']);
        });
        Schema::create('crm_sales_opportunities', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('pipeline_id')->constrained('crm_sales_pipelines')->cascadeOnDelete();
            $t->foreignId('stage_id')->constrained('crm_sales_stages')->restrictOnDelete();
            $t->unsignedBigInteger('contact_id')->nullable();
            $t->string('name');
            $t->decimal('value', 20, 6)->default(0);
            $t->decimal('probability', 5, 2)->default(0);
            $t->date('close_date')->nullable();
            $t->string('status')->default('open');
            $t->string('loss_reason')->nullable();
            $t->json('products')->nullable();
            $t->json('competitors')->nullable();
            $t->json('dependencies')->nullable();
            $t->timestamp('last_stage_at')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status', 'stage_id']);
        });
        Schema::create('crm_sales_stage_history', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('opportunity_id')->constrained('crm_sales_opportunities')->cascadeOnDelete();
            $t->unsignedBigInteger('from_stage_id')->nullable();
            $t->unsignedBigInteger('to_stage_id');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->timestamp('entered_at');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_sales_stage_history', 'crm_sales_opportunities', 'crm_sales_stages', 'crm_sales_pipelines'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
