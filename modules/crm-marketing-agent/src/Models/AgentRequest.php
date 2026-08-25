<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $team_id @property string $kind @property string $status */
final class AgentRequest extends Model
{
    protected $table = 'crm_marketing_agent_requests';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['audience' => 'array', 'result' => 'array', 'metadata' => 'array'];
    }

    public function checks(): HasMany
    {
        return $this->hasMany(AgentCheck::class, 'request_id');
    }
}
