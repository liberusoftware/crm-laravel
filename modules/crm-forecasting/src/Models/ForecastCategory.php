<?php

declare(strict_types=1);

namespace Liberu\CRM\Forecasting\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $code */
final class ForecastCategory extends Model
{
    use IsTenantModel;

    protected $table = 'crm_forecast_categories';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}
