<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Models;

use Illuminate\Database\Eloquent\Model;

final class AgentKnowledge extends Model
{
    protected $table = 'crm_service_agent_knowledge';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['tags' => 'array', 'active' => 'boolean'];
    }
}
