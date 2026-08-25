<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinars\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property string $status
 * @property int|null $capacity
 */
final class CrmEvent extends Model
{
    protected $table = 'crm_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'provider' => 'array', 'settings' => 'array'];
    }
}
