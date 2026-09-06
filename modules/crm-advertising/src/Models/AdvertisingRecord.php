<?php

declare(strict_types=1);

namespace Liberu\CRM\Advertising\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $kind
 * @property string $name
 * @property string $status
 * @property string|null $external_id
 * @property string|null $platform
 * @property array<string, mixed>|null $payload
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property Carbon|null $completed_at
 */
final class AdvertisingRecord extends Model
{
    use IsTenantModel;

    public const KINDS = ['ad_account_connection', 'campaign', 'lead_ad', 'crm_audience', 'offline_conversion', 'performance_sync', 'attribution'];

    public const STATUSES = ['draft', 'active', 'paused', 'completed', 'archived'];

    protected $table = 'crm_advertising_records';

    protected $fillable = ['team_id', 'kind', 'name', 'status', 'external_id', 'platform', 'payload', 'starts_at', 'ends_at', 'completed_at'];

    protected $casts = ['team_id' => 'integer', 'payload' => 'array', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'completed_at' => 'datetime'];

    public function scopeForTeam(Builder $query, int $teamId): void
    {
        $query->where($query->qualifyColumn('team_id'), $teamId);
    }

    public function transitionTo(string $status): void
    {
        if (! in_array($status, self::STATUSES, true)) {
            throw new \InvalidArgumentException('Unsupported advertising status.');
        }
        if (in_array($this->status, ['completed', 'archived'], true) && $status !== 'archived') {
            throw new \DomainException('A terminal advertising record cannot be reopened.');
        }
        $this->forceFill(['status' => $status, 'completed_at' => $status === 'completed' ? now() : $this->completed_at])->save();
    }
}
