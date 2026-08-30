<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformance\Models;

use Illuminate\Database\Eloquent\Model;

final class PerformanceReview extends Model
{
    protected $table = 'crm_performance_reviews';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['score' => 'integer', 'coaching_plan' => 'array'];
    }
}
