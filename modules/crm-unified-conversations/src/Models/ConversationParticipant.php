<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

final class ConversationParticipant extends Model
{
    use IsTenantModel;

    protected $table = 'crm_conversation_participants';

    protected $fillable = ['team_id', 'conversation_id', 'identity', 'name', 'role'];
}
