<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 */
final class Quota extends Model
{
    use IsTenantModel;

    protected $table = 'crm_quotas';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['target' => 'float', 'attained' => 'float', 'period_start' => 'date', 'period_end' => 'date', 'ramp' => 'array'];
    }
}
