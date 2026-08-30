<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\WorkManagement\Models\ChecklistItem;
use Liberu\CRM\WorkManagement\Models\WorkItem;
use Liberu\CRM\WorkManagement\Services\WorkAudit;

final class AddChecklistItem
{
    public function execute(WorkItem $item, ?int $actorId, string $title): ChecklistItem
    {
        $title = trim($title);
        if ($title === '') {
            throw ValidationException::withMessages(['title' => 'A checklist title is required.']);
        }
        $check = $item->checklist()->create(['team_id' => $item->team_id, 'actor_id' => $actorId, 'title' => $title, 'position' => (int) $item->checklist()->max('position') + 1]);
        app(WorkAudit::class)->record($item, $actorId, 'checklist_item.created', ['checklist_item_id' => $check->getKey()]);

        return $check->refresh();
    }
}
