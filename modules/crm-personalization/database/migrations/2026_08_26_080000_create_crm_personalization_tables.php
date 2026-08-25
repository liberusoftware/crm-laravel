<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_personalization_rules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->string('kind');
            $table->json('conditions');
            $table->json('variants');
            $table->json('fallback')->nullable();
            $table->unsignedTinyInteger('holdout_percent')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
        });
        Schema::create('crm_personalization_decisions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('rule_id');
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->string('variant');
            $table->string('channel');
            $table->string('locale');
            $table->json('attributes')->nullable();
            $table->boolean('holdout')->default(false);
            $table->timestamp('decided_at');
            $table->timestamps();
            $table->index(['team_id', 'subject_type', 'subject_id']);
        });
        Schema::create('crm_personalization_outcomes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('decision_id');
            $table->string('event');
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_personalization_outcomes');
        Schema::dropIfExists('crm_personalization_decisions');
        Schema::dropIfExists('crm_personalization_rules');
    }
};
