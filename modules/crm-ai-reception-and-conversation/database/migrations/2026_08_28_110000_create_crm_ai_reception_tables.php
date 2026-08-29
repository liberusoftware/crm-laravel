<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_ai_reception_agents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name');
            $table->string('channel')->default('chat');
            $table->string('status')->default('draft');
            $table->boolean('requires_human_approval')->default(true);
            $table->json('knowledge')->nullable();
            $table->json('tools')->nullable();
            $table->json('policy')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_ai_reception_conversations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('agent_id')->constrained('crm_ai_reception_agents')->cascadeOnDelete();
            $table->string('external_key')->nullable();
            $table->string('status')->default('active');
            $table->string('handoff_status')->default('not_requested');
            $table->decimal('confidence', 5, 4)->nullable();
            $table->json('transcript')->nullable();
            $table->json('qualification')->nullable();
            $table->json('booking')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_ai_reception_audits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('conversation_id')->constrained('crm_ai_reception_conversations')->cascadeOnDelete();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('type');
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_ai_reception_audits');
        Schema::dropIfExists('crm_ai_reception_conversations');
        Schema::dropIfExists('crm_ai_reception_agents');
    }
};
