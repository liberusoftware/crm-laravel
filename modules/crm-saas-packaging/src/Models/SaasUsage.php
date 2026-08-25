<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $quantity */
final class SaasUsage extends Model
{
    protected $table = 'crm_saas_usage';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['period_start' => 'datetime', 'period_end' => 'datetime'];
    }
}
