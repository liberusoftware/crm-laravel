<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Models;

use Illuminate\Database\Eloquent\Model;

final class ResourceForecast extends Model
{
    protected $table = 'crm_resource_forecasts';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['demand_hours' => 'float', 'available_hours' => 'float', 'period_start' => 'date', 'period_end' => 'date', 'assumptions' => 'array'];
    }
}
