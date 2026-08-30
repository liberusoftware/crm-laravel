<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Models;

use Illuminate\Database\Eloquent\Model;

final class ActivityEvent extends Model
{
    protected $table = 'crm_activity_events';

    protected $fillable = ['team_id', 'activity_id', 'actor_id', 'event', 'details'];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }
}
