<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_project_templates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->json('milestones')->nullable();
            $table->json('tasks')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('crm_projects', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('opportunity_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('planning');
            $table->boolean('client_visible')->default(false);
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_project_tasks', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('milestone_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('depends_on_id')->nullable();
            $table->string('name');
            $table->string('status')->default('open');
            $table->text('description')->nullable();
            $table->date('due_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_project_time', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('task_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->decimal('hours', 8, 2);
            $table->date('worked_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_project_risks', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('project_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('severity')->default('medium');
            $table->string('status')->default('open');
            $table->json('mitigation')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_project_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('project_id');
            $table->string('type');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_project_events');
        Schema::dropIfExists('crm_project_risks');
        Schema::dropIfExists('crm_project_time');
        Schema::dropIfExists('crm_project_tasks');
        Schema::dropIfExists('crm_projects');
        Schema::dropIfExists('crm_project_templates');
    }
};
