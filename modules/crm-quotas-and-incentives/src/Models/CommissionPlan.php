<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Models;

use Illuminate\Database\Eloquent\Model;

/** @property float $rate */
final class CommissionPlan extends Model
{
    protected $table = 'crm_commission_plans';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['rate' => 'float', 'accelerators' => 'array', 'active' => 'boolean'];
    }
}
