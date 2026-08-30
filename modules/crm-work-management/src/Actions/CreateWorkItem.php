<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\WorkManagement\Models\WorkItem;
use Liberu\CRM\WorkManagement\Services\WorkAudit;

final class CreateWorkItem
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, ?int $actorId, array $attributes): WorkItem
    {
        $title = trim((string) ($attributes['title'] ?? ''));
        if ($title === '') {
            throw ValidationException::withMessages(['title' => 'A title is required.']);
        }
        if (isset($attributes['recurrence']) && ! preg_match('/^[a-z0-9_\- ]{1,80}$/i', (string) $attributes['recurrence'])) {
            throw ValidationException::withMessages(['recurrence' => 'The recurrence expression is invalid.']);
        }

        return DB::transaction(function () use ($teamId, $actorId, $attributes, $title): WorkItem {
            $item = WorkItem::query()->create(array_merge($attributes, ['team_id' => $teamId, 'actor_id' => $actorId, 'title' => $title, 'version' => 1]));
            app(WorkAudit::class)->record($item, $actorId, 'work_item.created', ['status' => $item->status]);

            return $item->refresh();
        });
    }
}
