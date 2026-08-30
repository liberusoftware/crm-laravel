<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformance\Models;

use Illuminate\Database\Eloquent\Model;

/** @property float|null $value */
final class PerformanceEvent extends Model
{
    protected $table = 'crm_performance_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'decimal:2', 'payload' => 'array'];
    }
}
