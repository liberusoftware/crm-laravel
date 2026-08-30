<?php

declare(strict_types=1);

namespace Liberu\CRM\Collaboration\Actions;

use Liberu\CRM\Collaboration\Models\CollaborationWork;

final class AssignCollaborationWork
{
    public function execute(int $teamId, string $queueKey, string $subjectKey, ?string $assigneeKey = null, array $metadata = []): CollaborationWork
    {
        abort_unless($queueKey !== '' && $subjectKey !== '', 422);

        return CollaborationWork::query()->updateOrCreate(['team_id' => $teamId, 'queue_key' => $queueKey, 'subject_key' => $subjectKey], ['assignee_key' => $assigneeKey, 'status' => 'open', 'metadata' => $metadata]);
    }
}
