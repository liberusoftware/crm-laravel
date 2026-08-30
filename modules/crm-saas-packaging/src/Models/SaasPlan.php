<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $trial_days */
final class SaasPlan extends Model
{
    protected $table = 'crm_saas_plans';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['price' => 'float', 'entitlements' => 'array', 'limits' => 'array', 'active' => 'boolean'];
    }
}
