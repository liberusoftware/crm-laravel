<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $duration_minutes
 * @property int $buffer_before
 * @property int $buffer_after
 * @property int $minimum_notice_minutes
 */
final class SchedulingLink extends Model
{
    protected $table = 'crm_scheduling_links';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['availability' => 'array', 'questions' => 'array', 'reminders' => 'array', 'routing' => 'array', 'active' => 'boolean'];
    }
}
