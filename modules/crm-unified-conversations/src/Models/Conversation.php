<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Models;

use Illuminate\Database\Eloquent\Model;

final class Conversation extends Model
{
    protected $table = 'crm_conversations';

    protected $fillable = ['team_id', 'channel', 'external_id', 'subject', 'status', 'assigned_to'];

    protected function casts(): array
    {
        return ['team_id' => 'integer', 'assigned_to' => 'integer'];
    }
}
