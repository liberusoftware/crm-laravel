<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $response_minutes
 * @property int $resolution_minutes
 * @property int $warning_minutes
 */
final class SlaEntitlement extends Model
{
    protected $table = 'crm_sla_entitlements';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['coverage' => 'array', 'active' => 'boolean'];
    }
}
