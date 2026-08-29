<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspace\Queries;

use Liberu\CRM\SalesWorkspace\Models\WorkspaceItem;

final class WorkspaceQuery
{
    public function feed(int $teamId)
    {
        return WorkspaceItem::query()->where('team_id', $teamId)->orderByRaw("CASE priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'normal' THEN 2 ELSE 3 END")->orderBy('due_at');
    }

    public function overdue(int $teamId)
    {
        return WorkspaceItem::query()->where('team_id', $teamId)->whereNotIn('status', ['completed', 'closed'])->where('due_at', '<', now());
    }

    public function agenda(int $teamId)
    {
        return WorkspaceItem::query()->where('team_id', $teamId)->whereBetween('due_at', [now()->startOfDay(), now()->endOfDay()])->orderBy('due_at');
    }
}
