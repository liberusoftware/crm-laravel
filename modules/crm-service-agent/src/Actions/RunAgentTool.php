<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\ServiceAgent\Models\AgentCase;
use Liberu\CRM\ServiceAgent\Models\AgentToolRun;
use Liberu\CRM\ServiceAgent\Services\AgentAudit;
use Liberu\CRM\ServiceAgent\Services\AgentPolicy;

final class RunAgentTool
{
    public function execute(int $teamId, int $actorId, array $data): AgentToolRun
    {
        if (! app(AgentPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['case_id' => ['required', 'integer'], 'tool' => ['required', 'string', 'max:100'], 'input' => ['nullable', 'array']])->validate();
        if (! AgentCase::query()->where('team_id', $teamId)->whereKey($data['case_id'])->exists()) {
            throw ValidationException::withMessages(['case_id' => 'Case does not belong to this team.']);
        }$run = AgentToolRun::query()->create(['team_id' => $teamId, 'case_id' => $data['case_id'], 'tool' => $data['tool'], 'status' => 'queued', 'input' => $data['input'] ?? []]);
        app(AgentAudit::class)->record($teamId, $actorId, 'tool_queued', ['tool_run_id' => $run->id]);

        return $run;
    }
}
