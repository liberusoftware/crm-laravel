<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\ServiceAgent\Events\AgentCaseUpdated;
use Liberu\CRM\ServiceAgent\Models\AgentCase;
use Liberu\CRM\ServiceAgent\Services\AgentAudit;
use Liberu\CRM\ServiceAgent\Services\AgentPolicy;

final class EscalateAgentCase
{
    public function execute(int $teamId, int $actorId, int $caseId, array $data = []): AgentCase
    {
        if (! app(AgentPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }$case = AgentCase::query()->where('team_id', $teamId)->findOrFail($caseId);
        $case->escalation_level++;
        $case->status = 'escalated';
        $case->metadata = array_merge($case->metadata ?? [], ['escalation_reason' => $data['reason'] ?? 'Low confidence or operator request']);
        $case->save();
        app(AgentAudit::class)->record($teamId, $actorId, 'case_escalated', ['case_id' => $case->id, 'level' => $case->escalation_level]);
        AgentCaseUpdated::dispatch($case, 'escalated');

        return $case;
    }
}
