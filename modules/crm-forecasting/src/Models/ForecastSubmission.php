<?php

declare(strict_types=1);

namespace Liberu\CRM\Forecasting\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $forecast_id */
final class ForecastSubmission extends Model
{
    use IsTenantModel;

    protected $table = 'crm_forecast_submissions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'submitted_at' => 'datetime'];
    }
}
