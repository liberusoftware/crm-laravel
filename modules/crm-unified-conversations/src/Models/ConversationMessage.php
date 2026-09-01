<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

final class ConversationMessage extends Model
{
    use IsTenantModel;

    protected $table = 'crm_conversation_messages';

    protected $fillable = ['team_id', 'conversation_id', 'external_id', 'sender_id', 'body', 'direction', 'metadata', 'internal', 'delivery_status', 'read_at', 'idempotency_key'];

    protected function casts(): array
    {
        return ['internal' => 'boolean', 'read_at' => 'datetime', 'metadata' => 'array'];
    }
}
