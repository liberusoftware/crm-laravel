<?php

declare(strict_types=1);

namespace Liberu\CRM\Forecasting\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $forecast_id @property float $amount */
final class ForecastAdjustment extends Model
{
    protected $table = 'crm_forecast_adjustments';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }
}
