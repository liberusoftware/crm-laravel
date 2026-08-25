<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Models;

use Illuminate\Database\Eloquent\Model;

final class SlaEscalation extends Model
{
    protected $table = 'crm_sla_escalations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['triggered_at' => 'datetime', 'resolved_at' => 'datetime'];
    }
}
