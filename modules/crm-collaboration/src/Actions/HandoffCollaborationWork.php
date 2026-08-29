<?php

declare(strict_types=1);

namespace Liberu\CRM\Collaboration\Actions;

use Liberu\CRM\Collaboration\Models\CollaborationWork;

final class HandoffCollaborationWork
{
    public function execute(int $teamId, CollaborationWork $work, string $assigneeKey, string $reason): CollaborationWork
    {
        abort_unless((int) $work->team_id === $teamId && $work->status === 'open' && $assigneeKey !== '' && trim($reason) !== '', 422);
        $work->update(['assignee_key' => $assigneeKey, 'handoff_reason' => $reason]);

        return $work->refresh();
    }
}
