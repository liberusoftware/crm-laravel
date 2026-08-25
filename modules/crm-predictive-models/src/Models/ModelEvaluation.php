<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Models;

use Illuminate\Database\Eloquent\Model;

final class ModelEvaluation extends Model
{
    protected $table = 'crm_predictive_evaluations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['accuracy' => 'float', 'precision_score' => 'float', 'recall' => 'float', 'metrics' => 'array', 'evaluated_at' => 'datetime'];
    }
}
