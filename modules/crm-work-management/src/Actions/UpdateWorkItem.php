<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\WorkManagement\Models\WorkItem;
use Liberu\CRM\WorkManagement\Services\WorkAudit;

final class UpdateWorkItem
{
    /** @param array<string, mixed> $attributes */
    public function execute(WorkItem $item, ?int $actorId, array $attributes, ?int $expectedVersion = null): WorkItem
    {
        $editable = ['assigned_to', 'queue_id', 'title', 'description', 'status', 'priority', 'due_at', 'recurrence', 'next_run_at', 'metadata'];
        $unknown = array_diff(array_keys($attributes), $editable);
        if ($unknown !== []) {
            throw ValidationException::withMessages(['attributes' => 'The work item contains non-editable fields.']);
        }
        if ($expectedVersion !== null && $expectedVersion !== $item->version) {
            throw ValidationException::withMessages(['version' => 'The work item has changed since it was read.']);
        }
        if (isset($attributes['title']) && trim((string) $attributes['title']) === '') {
            throw ValidationException::withMessages(['title' => 'A work item title is required.']);
        }
        if (isset($attributes['status']) && ! in_array($attributes['status'], ['pending', 'in_progress', 'blocked', 'completed', 'cancelled'], true)) {
            throw ValidationException::withMessages(['status' => 'Unsupported work item status.']);
        }
        if (isset($attributes['priority']) && ! in_array($attributes['priority'], ['low', 'normal', 'high', 'urgent'], true)) {
            throw ValidationException::withMessages(['priority' => 'Unsupported work item priority.']);
        }

        return DB::transaction(function () use ($item, $actorId, $attributes): WorkItem {
            $item->update(array_merge($attributes, ['version' => $item->version + 1]));
            app(WorkAudit::class)->record($item, $actorId, 'work_item.updated', ['fields' => array_keys($attributes)]);

            return $item->refresh();
        });
    }
}
