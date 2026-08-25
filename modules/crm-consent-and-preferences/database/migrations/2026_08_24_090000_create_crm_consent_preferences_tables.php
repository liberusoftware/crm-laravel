<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_consent_records', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('subject_type', 120);
            $table->unsignedBigInteger('subject_id');
            $table->string('channel', 40);
            $table->string('topic', 120)->default('general');
            $table->string('lawful_basis', 40);
            $table->string('status', 24)->default('granted');
            $table->string('source', 120);
            $table->json('proof')->nullable();
            $table->timestamp('consented_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'subject_type', 'subject_id', 'channel', 'topic']);
        });
        Schema::create('crm_preference_records', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('subject_type', 120);
            $table->unsignedBigInteger('subject_id');
            $table->string('channel', 40);
            $table->string('topic', 120)->default('general');
            $table->string('state', 24)->default('allowed');
            $table->json('quiet_hours')->nullable();
            $table->string('timezone')->default('UTC');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->timestamps();
            $table->unique(
                ['team_id', 'subject_type', 'subject_id', 'channel', 'topic'],
                'crm_preferences_subject_channel_topic_unique',
            );
        });
        Schema::create('crm_suppression_records', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('subject_type', 120);
            $table->unsignedBigInteger('subject_id');
            $table->string('channel', 40)->nullable();
            $table->string('topic', 120)->nullable();
            $table->string('reason', 160);
            $table->string('source', 120);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'subject_type', 'subject_id']);
        });
        Schema::create('crm_policy_evaluations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('subject_type', 120);
            $table->unsignedBigInteger('subject_id');
            $table->string('channel', 40);
            $table->string('topic', 120)->default('general');
            $table->boolean('allowed');
            $table->json('reasons');
            $table->timestamp('evaluated_at');
            $table->timestamps();
            $table->index(['team_id', 'subject_type', 'subject_id', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_policy_evaluations');
        Schema::dropIfExists('crm_suppression_records');
        Schema::dropIfExists('crm_preference_records');
        Schema::dropIfExists('crm_consent_records');
    }
};
