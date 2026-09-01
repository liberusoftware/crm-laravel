<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanning\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $kind
 * @property string $name
 * @property string $status
 * @property int|null $account_id
 * @property int|null $owner_id
 * @property array<string, mixed>|null $payload
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property Carbon|null $completed_at
 */
final class AccountPlanningRecord extends Model
{
    use IsTenantModel;

    public const KINDS = ['account_hierarchy', 'stakeholder', 'influence_map', 'whitespace', 'account_plan', 'objective', 'risk', 'mutual_action_plan', 'coordination'];

    public const STATUSES = ['draft', 'active', 'paused', 'completed', 'archived'];

    protected $table = 'crm_account_planning_records';

    protected $fillable = ['team_id', 'kind', 'name', 'status', 'account_id', 'owner_id', 'payload', 'starts_at', 'ends_at', 'completed_at'];

    protected $casts = ['team_id' => 'integer', 'account_id' => 'integer', 'owner_id' => 'integer', 'payload' => 'array', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'completed_at' => 'datetime'];

    public function scopeForTeam(Builder $query, int $teamId): void
    {
        $query->where($query->qualifyColumn('team_id'), $teamId);
    }

    public function transitionTo(string $status): void
    {
        if (! in_array($status, self::STATUSES, true)) {
            throw new \InvalidArgumentException('Unsupported account planning status.');
        }
        if (in_array($this->status, ['completed', 'archived'], true) && $status !== 'archived') {
            throw new \DomainException('A terminal account planning record cannot be reopened.');
        }
        $this->forceFill(['status' => $status, 'completed_at' => $status === 'completed' ? now() : $this->completed_at])->save();
    }
}
