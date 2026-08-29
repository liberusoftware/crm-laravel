<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Models;

use Illuminate\Database\Eloquent\Model;

final class AgentAudit extends Model
{
    protected $table = 'crm_service_agent_audits';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }
}
