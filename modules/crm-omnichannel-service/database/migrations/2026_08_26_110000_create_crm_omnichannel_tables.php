<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_omnichannel_conversations', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('external_key');
            $t->string('channel');
            $t->string('status')->default('open');
            $t->string('priority')->default('normal');
            $t->unsignedBigInteger('assigned_to')->nullable();
            $t->string('subject')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'external_key']);
            $t->index(['team_id', 'status', 'assigned_to']);
        });
        Schema::create('crm_omnichannel_interactions', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('conversation_id')->constrained('crm_omnichannel_conversations')->cascadeOnDelete();
            $t->string('direction');
            $t->string('author_type')->nullable();
            $t->unsignedBigInteger('author_id')->nullable();
            $t->text('body');
            $t->string('external_key')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamp('occurred_at');
            $t->timestamps();
            $t->index(
                ['team_id', 'conversation_id', 'occurred_at'],
                'crm_omnichannel_interaction_time_index',
            );
        });
        Schema::create('crm_omnichannel_macros', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->text('body');
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
        Schema::create('crm_omnichannel_workspace_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('conversation_id')->constrained('crm_omnichannel_conversations')->cascadeOnDelete();
            $t->string('kind');
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('status')->default('active');
            $t->text('payload')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
            $t->index(
                ['team_id', 'conversation_id', 'kind', 'status'],
                'crm_omnichannel_workspace_event_index',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_omnichannel_workspace_events');
        Schema::dropIfExists('crm_omnichannel_macros');
        Schema::dropIfExists('crm_omnichannel_interactions');
        Schema::dropIfExists('crm_omnichannel_conversations');
    }
};
