<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBots\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $owner_id
 * @property string $name
 * @property string $status
 * @property array<string, mixed> $playbook
 */
final class ChatBot extends Model
{
    use IsTenantModel;

    protected $table = 'crm_chat_bots';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['playbook' => 'array', 'office_hours' => 'array', 'channels' => 'array'];
    }
}
