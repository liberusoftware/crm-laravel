<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Models;

use Illuminate\Database\Eloquent\Model;

final class ResourceRate extends Model
{
    protected $table = 'crm_resource_rates';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['hourly_rate' => 'float', 'effective_from' => 'date', 'effective_until' => 'date'];
    }
}
