<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgent\Models;

use Illuminate\Database\Eloquent\Model;

/** @property string $status */
final class AgentExperiment extends Model
{
    protected $table = 'crm_marketing_agent_experiments';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['variants' => 'array', 'metrics' => 'array'];
    }
}
