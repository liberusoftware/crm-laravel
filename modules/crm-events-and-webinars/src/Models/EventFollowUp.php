<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinars\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $event_id */
final class EventFollowUp extends Model
{
    protected $table = 'crm_event_follow_ups';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'scheduled_at' => 'datetime'];
    }
}
