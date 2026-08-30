<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Models;

use Illuminate\Database\Eloquent\Model;

final class ConversationMessage extends Model
{
    protected $table = 'crm_conversation_messages';

    protected $fillable = ['team_id', 'conversation_id', 'sender_id', 'body', 'internal', 'delivery_status', 'read_at', 'idempotency_key'];

    protected function casts(): array
    {
        return ['internal' => 'boolean', 'read_at' => 'datetime'];
    }
}
