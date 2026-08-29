<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Models;

use Illuminate\Database\Eloquent\Model;

final class SchedulingAudit extends Model
{
    protected $table = 'crm_scheduling_audits';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }
}
