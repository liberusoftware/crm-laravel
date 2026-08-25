<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Models;

use Illuminate\Database\Eloquent\Model;

final class Quota extends Model
{
    protected $table = 'crm_quotas';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['target' => 'float', 'attained' => 'float', 'period_start' => 'date', 'period_end' => 'date', 'ramp' => 'array'];
    }
}
