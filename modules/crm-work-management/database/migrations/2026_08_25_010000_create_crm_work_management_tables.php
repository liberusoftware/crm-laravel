<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_work_queues', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('name', 160);
            $table->string('description', 500)->nullable();
            $table->string('status', 24)->default('active');
            $table->json('rules')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'name']);
        });

        Schema::create('crm_work_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable()->index();
            $table->foreignId('queue_id')->nullable()->constrained('crm_work_queues')->nullOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('status', 24)->default('pending');
            $table->string('priority', 16)->default('normal');
            $table->string('subject_type', 160)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->timestamp('due_at')->nullable()->index();
            $table->string('recurrence', 80)->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'status', 'assigned_to']);
        });

        Schema::create('crm_work_checklist_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_item_id')->constrained('crm_work_items')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('title', 200);
            $table->boolean('completed')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('crm_work_approvals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_item_id')->constrained('crm_work_items')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->string('status', 24)->default('pending');
            $table->text('comment')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_work_dependencies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_item_id')->constrained('crm_work_items')->cascadeOnDelete();
            $table->foreignId('depends_on_id')->constrained('crm_work_items')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->index();
            $table->timestamps();
            $table->unique(['work_item_id', 'depends_on_id']);
        });

        Schema::create('crm_work_audits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('work_item_id')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('event', 80);
            $table->json('details')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_work_audits');
        Schema::dropIfExists('crm_work_dependencies');
        Schema::dropIfExists('crm_work_approvals');
        Schema::dropIfExists('crm_work_checklist_items');
        Schema::dropIfExists('crm_work_items');
        Schema::dropIfExists('crm_work_queues');
    }
};
