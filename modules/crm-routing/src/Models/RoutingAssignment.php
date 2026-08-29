<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $team_id
 * @property string $status
 * @property Carbon|null $accepted_at
 */
final class RoutingAssignment extends Model
{
    protected $table = 'crm_routing_assignments';

    protected $guarded = [];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(RoutingAgent::class, 'agent_id');
    }

    protected function casts(): array
    {
        return ['acceptance_due_at' => 'datetime', 'accepted_at' => 'datetime', 'fallback_at' => 'datetime', 'criteria' => 'array'];
    }
}
