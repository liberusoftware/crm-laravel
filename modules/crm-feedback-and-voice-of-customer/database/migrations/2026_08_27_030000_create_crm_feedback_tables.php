<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_feedback_surveys', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('name');
            $table->string('slug');
            $table->string('metric')->default('csat');
            $table->string('status')->default('draft');
            $table->json('questions');
            $table->json('sampling')->nullable();
            $table->json('delivery')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'slug']);
        });
        Schema::create('crm_feedback_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('survey_id')->constrained('crm_feedback_surveys');
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->string('channel')->default('email');
            $table->string('status')->default('pending');
            $table->string('token')->unique();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_feedback_responses', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('survey_id')->constrained('crm_feedback_surveys');
            $table->foreignId('delivery_id')->nullable()->constrained('crm_feedback_deliveries');
            $table->unsignedBigInteger('respondent_id')->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('comment')->nullable();
            $table->string('sentiment')->nullable();
            $table->json('answers')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'survey_id']);
        });
        Schema::create('crm_feedback_alerts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('response_id')->constrained('crm_feedback_responses');
            $table->string('reason');
            $table->string('status')->default('open');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_feedback_cases', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('response_id')->constrained('crm_feedback_responses');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('status')->default('open');
            $table->text('resolution')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_feedback_cases');
        Schema::dropIfExists('crm_feedback_alerts');
        Schema::dropIfExists('crm_feedback_responses');
        Schema::dropIfExists('crm_feedback_deliveries');
        Schema::dropIfExists('crm_feedback_surveys');
    }
};
