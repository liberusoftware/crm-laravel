<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Models;

use Illuminate\Database\Eloquent\Model;

final class AgentToolRun extends Model
{
    protected $table = 'crm_service_agent_tool_runs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['input' => 'array', 'output' => 'array'];
    }
}
