<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $team_id
 * @property int|null $actor_id
 * @property string $kind
 * @property string $status
 * @property string|null $recurrence
 * @property Carbon|null $starts_at
 * @property Carbon|null $due_at
 * @property Carbon|null $recurrence_until
 * @property Carbon|null $reminder_at
 * @property Carbon|null $completed_at
 */
final class Activity extends Model
{
    protected $table = 'crm_activities';

    protected $fillable = ['team_id', 'actor_id', 'assigned_to', 'kind', 'status', 'title', 'description', 'subject_type', 'subject_id', 'starts_at', 'due_at', 'ends_at', 'recurrence', 'recurrence_until', 'reminder_at', 'queue', 'outcome', 'outcome_notes', 'metadata', 'completed_at'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'starts_at' => 'datetime', 'due_at' => 'datetime', 'ends_at' => 'datetime', 'recurrence_until' => 'datetime', 'reminder_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    /** @return HasMany<ActivityEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(ActivityEvent::class, 'activity_id');
    }

    public function isOpen(): bool
    {
        return ! in_array($this->status, ['completed', 'cancelled'], true);
    }
}
