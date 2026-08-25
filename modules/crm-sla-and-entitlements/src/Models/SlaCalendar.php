<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Models;

use Illuminate\Database\Eloquent\Model;

final class SlaCalendar extends Model
{
    protected $table = 'crm_sla_calendars';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['weekly_schedule' => 'array', 'holidays' => 'array', 'active' => 'boolean'];
    }
}
