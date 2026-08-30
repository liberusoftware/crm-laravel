<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_email_mailboxes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('provider');
            $table->string('address');
            $table->string('status')->default('connected');
            $table->text('credential_reference')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'address']);
        });
        Schema::create('crm_email_templates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name');
            $table->string('kind')->default('template');
            $table->text('subject');
            $table->longText('body');
            $table->boolean('shared')->default(false);
            $table->timestamps();
        });
        Schema::create('crm_email_messages', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreignId('mailbox_id')->nullable()->constrained('crm_email_mailboxes');
            $table->string('message_id')->nullable();
            $table->string('thread_key')->nullable();
            $table->string('direction')->default('outbound');
            $table->string('status')->default('draft');
            $table->string('to_address');
            $table->string('subject');
            $table->longText('body');
            $table->json('tracking')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'thread_key']);
        });
        Schema::create('crm_email_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('message_id')->constrained('crm_email_messages');
            $table->string('event');
            $table->timestamp('occurred_at');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_email_events');
        Schema::dropIfExists('crm_email_messages');
        Schema::dropIfExists('crm_email_templates');
        Schema::dropIfExists('crm_email_mailboxes');
    }
};
