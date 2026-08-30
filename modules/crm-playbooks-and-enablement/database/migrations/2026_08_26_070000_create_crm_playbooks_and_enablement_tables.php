<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_playbooks', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->string('kind');
            $table->text('description')->nullable();
            $table->json('steps')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('crm_playbook_assignments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('playbook_id');
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('assigned');
            $table->json('evidence')->nullable();
            $table->json('checklist')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'playbook_id', 'user_id']);
        });
        Schema::create('crm_playbook_recommendations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('playbook_id');
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->string('reason');
            $table->string('status')->default('recommended');
            $table->timestamps();
        });
        Schema::create('crm_playbook_usage', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('playbook_id');
            $table->unsignedBigInteger('user_id');
            $table->string('event');
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_playbook_usage');
        Schema::dropIfExists('crm_playbook_recommendations');
        Schema::dropIfExists('crm_playbook_assignments');
        Schema::dropIfExists('crm_playbooks');
    }
};
