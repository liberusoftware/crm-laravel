<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Models;

use Illuminate\Database\Eloquent\Model;

final class PredictiveModel extends Model
{
    protected $table = 'crm_predictive_model_registry';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['configuration' => 'array'];
    }
}
