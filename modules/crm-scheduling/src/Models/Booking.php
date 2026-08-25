<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $status
 * @property Carbon $starts_at
 * @property Carbon $ends_at
 * @property string|null $cancel_reason
 */
final class Booking extends Model
{
    protected $table = 'crm_scheduling_bookings';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'answers' => 'array'];
    }
}
