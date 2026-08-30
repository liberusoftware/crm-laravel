<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Models;

use Illuminate\Database\Eloquent\Model;

final class AgentSequence extends Model
{
    protected $table = 'crm_prospecting_agent_sequences';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['dispatched_at' => 'datetime'];
    }
}
