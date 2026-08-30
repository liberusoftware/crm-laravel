<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $team_id
 * @property int|null $assigned_to
 * @property string $status
 * @property int $version
 * @property string|null $recurrence
 * @property Carbon|null $next_run_at
 */
final class WorkItem extends Model
{
    protected $table = 'crm_work_items';

    protected $fillable = ['team_id', 'actor_id', 'assigned_to', 'queue_id', 'title', 'description', 'status', 'priority', 'subject_type', 'subject_id', 'due_at', 'recurrence', 'next_run_at', 'version', 'metadata'];

    protected function casts(): array
    {
        return ['due_at' => 'datetime', 'next_run_at' => 'datetime', 'metadata' => 'array', 'version' => 'integer'];
    }

    /** @return HasMany<ChecklistItem, $this> */
    public function checklist(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'work_item_id')->orderBy('position');
    }

    /** @return HasMany<Approval, $this> */
    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'work_item_id');
    }
}
