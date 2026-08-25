<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagement\Actions;

use Liberu\CRM\CaseManagement\Models\CaseAudit;
use Liberu\CRM\CaseManagement\Models\CaseRecord;

final class OpenCase
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, int $ownerId, array $input): CaseRecord
    {
        $key = trim((string) ($input['case_key'] ?? ''));
        $subject = trim((string) ($input['subject'] ?? ''));
        abort_unless($key !== '' && $subject !== '', 422);
        $case = CaseRecord::query()->create(['team_id' => $teamId, 'owner_id' => $ownerId, 'case_key' => $key, 'parent_id' => $input['parent_id'] ?? null, 'type' => $input['type'] ?? 'support', 'pipeline' => $input['pipeline'] ?? 'default', 'priority' => $input['priority'] ?? 'normal', 'subject' => $subject, 'description' => $input['description'] ?? null, 'related_refs' => $input['related_refs'] ?? [], 'entitlement' => $input['entitlement'] ?? []]);
        CaseAudit::query()->create(['team_id' => $teamId, 'case_id' => $case->id, 'actor_id' => $ownerId, 'event' => 'opened', 'after' => $case->toArray()]);

        return $case;
    }
}
