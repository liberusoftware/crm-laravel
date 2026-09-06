<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('crm_conversations', function (Blueprint $table): void {
            $table->string('priority')->default('normal')->after('status');
            $table->timestamp('last_message_at')->nullable()->after('assigned_to')->index();
            $table->json('metadata')->nullable()->after('last_message_at');
        });
        Schema::table('crm_conversation_messages', function (Blueprint $table): void {
            $table->string('external_id')->nullable()->after('conversation_id');
            $table->string('direction')->default('inbound')->after('body');
            $table->json('metadata')->nullable()->after('direction');
            $table->index(['team_id', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::table('crm_conversation_messages', function (Blueprint $table): void {
            $table->dropIndex(['team_id', 'external_id']);
            $table->dropColumn(['external_id', 'direction', 'metadata']);
        });
        Schema::table('crm_conversations', function (Blueprint $table): void {
            $table->dropColumn(['priority', 'last_message_at', 'metadata']);
        });
    }
};
