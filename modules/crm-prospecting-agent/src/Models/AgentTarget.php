<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Models;

use Illuminate\Database\Eloquent\Model;

final class AgentTarget extends Model
{
    protected $table = 'crm_prospecting_agent_targets';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['research' => 'array', 'personalization' => 'array'];
    }
}
