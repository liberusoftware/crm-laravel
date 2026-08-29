<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $team_id
 * @property string $status
 */
final class WorkQueue extends Model
{
    protected $table = 'crm_work_queues';

    protected $fillable = ['team_id', 'actor_id', 'name', 'description', 'status', 'rules'];

    protected function casts(): array
    {
        return ['rules' => 'array'];
    }

    /** @return HasMany<WorkItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(WorkItem::class, 'queue_id');
    }
}
