<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBots\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $bot_id
 * @property string $visitor_key
 * @property string $status
 * @property string|null $handoff_to
 */
final class ChatSession extends Model
{
    protected $table = 'crm_chat_sessions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['qualification' => 'array', 'transcript' => 'array'];
    }
}
