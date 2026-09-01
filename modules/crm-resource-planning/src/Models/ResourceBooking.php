<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $team_id
 * @property Carbon $starts_at
 * @property Carbon $ends_at
 */
final class ResourceBooking extends Model
{
    use IsTenantModel;

    protected $table = 'crm_resource_bookings';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'hours' => 'float', 'rate' => 'float', 'metadata' => 'array'];
    }
}
