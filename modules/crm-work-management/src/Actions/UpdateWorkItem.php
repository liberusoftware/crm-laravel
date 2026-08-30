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
        if ($expectedVersion !== null && $expectedVersion !== $item->version) {
            throw ValidationException::withMessages(['version' => 'The work item has changed since it was read.']);
        }
        if (isset($attributes['status']) && ! in_array($attributes['status'], ['pending', 'in_progress', 'blocked', 'completed', 'cancelled'], true)) {
            throw ValidationException::withMessages(['status' => 'Unsupported work item status.']);
        }

        return DB::transaction(function () use ($item, $actorId, $attributes): WorkItem {
            $item->update(array_merge($attributes, ['version' => $item->version + 1]));
            app(WorkAudit::class)->record($item, $actorId, 'work_item.updated', ['fields' => array_keys($attributes)]);

            return $item->refresh();
        });
    }
}
