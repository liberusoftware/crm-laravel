<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 */
final class Prediction extends Model
{
    protected $table = 'crm_predictive_predictions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['score' => 'float', 'explanation' => 'array', 'features' => 'array', 'predicted_at' => 'datetime'];
    }
}
