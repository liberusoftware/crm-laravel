<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Models;

use Illuminate\Database\Eloquent\Model;

final class ModelDrift extends Model
{
    protected $table = 'crm_predictive_drift';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['baseline' => 'float', 'observed' => 'float', 'threshold' => 'float', 'detected_at' => 'datetime'];
    }
}
