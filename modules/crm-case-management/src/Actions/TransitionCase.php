<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagement\Actions;

use Liberu\CRM\CaseManagement\Models\CaseAudit;
use Liberu\CRM\CaseManagement\Models\CaseRecord;

final class TransitionCase
{
    public function execute(int $teamId, int $actorId, CaseRecord $case, string $status, ?int $ownerId = null): CaseRecord
    {
        abort_unless((int) $case->team_id === $teamId && in_array($status, ['open', 'pending', 'resolved', 'closed', 'escalated'], true), 422);
        $before = $case->toArray();
        $case->update(['status' => $status, 'owner_id' => $ownerId ?? $case->owner_id, 'escalation_level' => $status === 'escalated' ? $case->escalation_level + 1 : $case->escalation_level]);
        CaseAudit::query()->create(['team_id' => $teamId, 'case_id' => $case->id, 'actor_id' => $actorId, 'event' => 'status_changed', 'before' => $before, 'after' => $case->fresh()->toArray()]);

        return $case->refresh();
    }
}
