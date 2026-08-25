<?php

declare(strict_types=1);

namespace Liberu\CRM\Advocacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $kind
 * @property string $name
 * @property string $status
 * @property int|null $contact_id
 * @property int|null $owner_id
 * @property array<string, mixed>|null $payload
 * @property Carbon|null $requested_at
 * @property Carbon|null $approved_at
 * @property Carbon|null $completed_at
 */
final class AdvocacyRecord extends Model
{
    public const KINDS = ['reference', 'testimonial', 'review', 'case_study_consent', 'speaker', 'advisory_board', 'nomination', 'request', 'recognition'];

    public const STATUSES = ['draft', 'requested', 'approved', 'active', 'completed', 'archived'];

    protected $table = 'crm_advocacy_records';

    protected $fillable = ['team_id', 'kind', 'name', 'status', 'contact_id', 'owner_id', 'payload', 'requested_at', 'approved_at', 'completed_at'];

    protected $casts = ['team_id' => 'integer', 'contact_id' => 'integer', 'owner_id' => 'integer', 'payload' => 'array', 'requested_at' => 'datetime', 'approved_at' => 'datetime', 'completed_at' => 'datetime'];

    public function scopeForTeam(Builder $query, int $teamId): void
    {
        $query->where($query->qualifyColumn('team_id'), $teamId);
    }

    public function transitionTo(string $status): void
    {
        if (! in_array($status, self::STATUSES, true)) {
            throw new \InvalidArgumentException('Unsupported advocacy status.');
        }
        if (in_array($this->status, ['completed', 'archived'], true) && $status !== 'archived') {
            throw new \DomainException('A terminal advocacy record cannot be reopened.');
        }
        $this->forceFill(['status' => $status, 'approved_at' => $status === 'approved' ? now() : $this->approved_at, 'completed_at' => $status === 'completed' ? now() : $this->completed_at])->save();
    }
}
