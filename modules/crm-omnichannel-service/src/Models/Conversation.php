<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $team_id @property string $channel @property string $status @property string $priority @property int|null $assigned_to */
final class Conversation extends Model
{
    protected $table = 'crm_omnichannel_conversations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class, 'conversation_id');
    }

    public function workspaceEvents(): HasMany
    {
        return $this->hasMany(WorkspaceEvent::class, 'conversation_id');
    }
}
