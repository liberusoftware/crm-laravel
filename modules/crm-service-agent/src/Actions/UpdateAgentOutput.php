<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\ServiceAgent\Events\AgentCaseUpdated;
use Liberu\CRM\ServiceAgent\Models\AgentCase;
use Liberu\CRM\ServiceAgent\Services\AgentAudit;
use Liberu\CRM\ServiceAgent\Services\AgentPolicy;

final class UpdateAgentOutput
{
    public function execute(int $teamId, int $actorId, int $caseId, string $type, array $data): AgentCase
    {
        if (! app(AgentPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }$case = AgentCase::query()->where('team_id', $teamId)->findOrFail($caseId);
        if ($type === 'draft') {
            validator($data, ['response_draft' => ['required', 'string']])->validate();
            $case->response_draft = $data['response_draft'];
            $operation = 'response_drafted';
        } else {
            validator($data, ['resolution_plan' => ['required', 'array']])->validate();
            $case->resolution_plan = $data['resolution_plan'];
            $operation = 'resolution_planned';
        }$case->status = 'in_progress';
        $case->save();
        app(AgentAudit::class)->record($teamId, $actorId, $operation, ['case_id' => $case->id]);
        AgentCaseUpdated::dispatch($case, $operation);

        return $case;
    }
}
