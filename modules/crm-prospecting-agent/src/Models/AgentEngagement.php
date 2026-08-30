<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Models;

use Illuminate\Database\Eloquent\Model;

final class AgentEngagement extends Model
{
    protected $table = 'crm_prospecting_agent_engagements';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'occurred_at' => 'datetime'];
    }
}
