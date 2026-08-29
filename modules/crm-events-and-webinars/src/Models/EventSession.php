<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinars\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $event_id */
final class EventSession extends Model
{
    protected $table = 'crm_event_sessions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'speakers' => 'array'];
    }
}
