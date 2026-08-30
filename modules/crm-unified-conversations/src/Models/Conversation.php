<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Conversation extends Model
{
    protected $table = 'crm_conversations';

    protected $fillable = ['team_id', 'channel', 'external_id', 'subject', 'status', 'priority', 'assigned_to', 'last_message_at', 'metadata'];

    protected function casts(): array
    {
        return ['team_id' => 'integer', 'assigned_to' => 'integer', 'last_message_at' => 'datetime', 'metadata' => 'array'];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }
}
