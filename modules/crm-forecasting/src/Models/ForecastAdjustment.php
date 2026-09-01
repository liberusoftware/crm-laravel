<?php

declare(strict_types=1);

namespace Liberu\CRM\Forecasting\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $forecast_id @property float $amount */
final class ForecastAdjustment extends Model
{
    use IsTenantModel;

    protected $table = 'crm_forecast_adjustments';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }
}
