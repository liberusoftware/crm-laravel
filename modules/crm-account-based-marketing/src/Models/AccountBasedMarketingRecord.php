<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketing\Models;

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
final class AccountBasedMarketingRecord extends Model
{
    public const KINDS = [
        'target_account',
        'tier',
        'buying_committee',
        'intent',
        'engagement_rollup',
        'account_audience',
        'play',
        'coverage',
        'pipeline_influence',
        'measurement',
    ];

    public const STATUSES = ['draft', 'active', 'paused', 'completed', 'archived'];

    protected $table = 'crm_abm_records';

    protected $fillable = [
        'team_id',
        'kind',
        'name',
        'status',
        'account_id',
        'owner_id',
        'payload',
        'starts_at',
        'ends_at',
        'completed_at',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'account_id' => 'integer',
        'owner_id' => 'integer',
        'payload' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function scopeForTeam(Builder $query, int $teamId): void
    {
        $query->where($query->qualifyColumn('team_id'), $teamId);
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, ['completed', 'archived'], true);
    }

    public function transitionTo(string $status): void
    {
        if (! in_array($status, self::STATUSES, true)) {
            throw new \InvalidArgumentException('Unsupported ABM record status.');
        }

        if ($this->isTerminal() && $status !== 'archived') {
            throw new \DomainException('A completed or archived ABM record cannot be reopened.');
        }

        $this->forceFill([
            'status' => $status,
            'completed_at' => $status === 'completed' ? now() : $this->completed_at,
        ])->save();
    }
}
