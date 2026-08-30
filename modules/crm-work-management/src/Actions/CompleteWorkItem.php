<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\WorkManagement\Models\WorkItem;
use Liberu\CRM\WorkManagement\Services\WorkAudit;

final class CompleteWorkItem
{
    public function execute(WorkItem $item, ?int $actorId, ?int $expectedVersion = null): WorkItem
    {
        if ($expectedVersion !== null && $expectedVersion !== $item->version) {
            throw ValidationException::withMessages(['version' => 'The work item has changed since it was read.']);
        }
        if ($item->status === 'cancelled') {
            throw ValidationException::withMessages(['status' => 'A cancelled work item cannot be completed.']);
        }

        return DB::transaction(function () use ($item, $actorId): WorkItem {
            $item->update(['status' => 'completed', 'version' => $item->version + 1]);
            app(WorkAudit::class)->record($item, $actorId, 'work_item.completed');
            if ($item->recurrence !== null) {
                WorkItem::query()->create($item->only(['team_id', 'assigned_to', 'queue_id', 'title', 'description', 'priority', 'subject_type', 'subject_id', 'recurrence', 'metadata']) + ['actor_id' => $actorId, 'status' => 'pending', 'next_run_at' => $item->next_run_at?->addDay(), 'version' => 1]);
            }

            return $item->refresh();
        });
    }
}
