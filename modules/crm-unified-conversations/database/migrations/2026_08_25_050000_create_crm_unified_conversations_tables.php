<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_conversations', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->string('channel');
            $t->string('external_id')->nullable();
            $t->string('subject')->nullable();
            $t->string('status')->default('open');
            $t->unsignedBigInteger('assigned_to')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'channel', 'external_id']);
        });
        Schema::create('crm_conversation_participants', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('conversation_id');
            $t->string('identity');
            $t->string('name')->nullable();
            $t->string('role')->default('customer');
            $t->timestamps();
            $t->unique(['conversation_id', 'identity']);
        });
        Schema::create('crm_conversation_messages', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('conversation_id');
            $t->unsignedBigInteger('sender_id')->nullable();
            $t->text('body');
            $t->boolean('internal')->default(false);
            $t->string('delivery_status')->default('pending');
            $t->timestamp('read_at')->nullable();
            $t->string('idempotency_key')->nullable();
            $t->timestamps();
            $t->unique(
                ['team_id', 'conversation_id', 'idempotency_key'],
                'crm_conversation_message_idempotency_unique',
            );
        });
        Schema::create('crm_conversation_audits', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('event');
            $t->json('details')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_conversation_audits', 'crm_conversation_messages', 'crm_conversation_participants', 'crm_conversations'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
