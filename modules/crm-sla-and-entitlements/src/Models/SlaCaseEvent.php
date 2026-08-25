<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Models;

use Illuminate\Database\Eloquent\Model;

final class SlaCaseEvent extends Model
{
    protected $table = 'crm_sla_case_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'occurred_at' => 'datetime'];
    }
}
