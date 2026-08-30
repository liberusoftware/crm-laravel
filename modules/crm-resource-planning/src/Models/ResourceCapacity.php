<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property float $available_hours
 * @property float $allocated_hours
 */
final class ResourceCapacity extends Model
{
    protected $table = 'crm_resource_capacity';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['available_hours' => 'float', 'allocated_hours' => 'float', 'period_start' => 'date', 'period_end' => 'date', 'metadata' => 'array'];
    }
}
