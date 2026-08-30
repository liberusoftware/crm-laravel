<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Models;

use Illuminate\Database\Eloquent\Model;

final class ConversationAudit extends Model
{
    protected $table = 'crm_conversation_audits';

    protected $fillable = ['team_id', 'actor_id', 'event', 'details'];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }
}
