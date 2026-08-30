<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\WorkManagement\Models\Dependency;
use Liberu\CRM\WorkManagement\Models\WorkItem;
use Liberu\CRM\WorkManagement\Services\WorkAudit;

final class AddDependency
{
    public function execute(WorkItem $item, WorkItem $dependsOn, ?int $actorId): Dependency
    {
        if ($item->team_id !== $dependsOn->team_id) {
            throw ValidationException::withMessages(['depends_on_id' => 'Dependencies must belong to the same team.']);
        }
        if ($item->is($dependsOn)) {
            throw ValidationException::withMessages(['depends_on_id' => 'A work item cannot depend on itself.']);
        }
        if (Dependency::query()->where('work_item_id', $item->getKey())->where('depends_on_id', $dependsOn->getKey())->exists()) {
            throw ValidationException::withMessages(['depends_on_id' => 'That dependency already exists.']);
        }
        $dependency = Dependency::query()->create(['work_item_id' => $item->getKey(), 'depends_on_id' => $dependsOn->getKey(), 'team_id' => $item->team_id]);
        app(WorkAudit::class)->record($item, $actorId, 'dependency.created', ['depends_on_id' => $dependsOn->getKey()]);

        return $dependency->refresh();
    }
}
