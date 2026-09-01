<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 */
final class AgentRun extends Model
{
    use IsTenantModel;

    protected $table = 'crm_prospecting_agent_runs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['approved' => 'boolean', 'targeting' => 'array', 'policy' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    }
}
