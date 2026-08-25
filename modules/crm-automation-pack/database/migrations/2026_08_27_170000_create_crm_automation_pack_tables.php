<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_automation_recipes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name');
            $table->string('status')->default('draft');
            $table->unsignedInteger('version')->default(1);
            $table->json('triggers');
            $table->json('conditions')->nullable();
            $table->json('actions');
            $table->boolean('approval_required')->default(true);
            $table->timestamps();
        });
        Schema::create('crm_automation_enrollments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('recipe_id')->constrained('crm_automation_recipes');
            $table->string('subject_key');
            $table->string('status')->default('enrolled');
            $table->json('history')->nullable();
            $table->timestamp('enrolled_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_automation_runs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('recipe_id')->constrained('crm_automation_recipes');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('kind')->default('simulation');
            $table->string('status')->default('pending');
            $table->json('input')->nullable();
            $table->json('output')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_automation_approvals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('recipe_id')->constrained('crm_automation_recipes');
            $table->unsignedBigInteger('actor_id');
            $table->string('status')->default('pending');
            $table->text('reason')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_automation_approvals');
        Schema::dropIfExists('crm_automation_runs');
        Schema::dropIfExists('crm_automation_enrollments');
        Schema::dropIfExists('crm_automation_recipes');
    }
};
