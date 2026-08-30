<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Services;

use Illuminate\Database\Eloquent\Collection;
use Liberu\CRM\WorkManagement\Models\WorkItem;

final class WorkloadQuery
{
    /** @return Collection<int, WorkItem> */
    public function forTeam(int $teamId): Collection
    {
        return WorkItem::query()->where('team_id', $teamId)->whereNotIn('status', ['completed', 'cancelled'])->selectRaw('assigned_to, count(*) as open_count, sum(case when due_at < ? then 1 else 0 end) as overdue_count', [now()])->groupBy('assigned_to')->orderByDesc('open_count')->get();
    }
}
