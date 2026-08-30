<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ProspectingAgent\Models\AgentRun;
use Liberu\CRM\ProspectingAgent\Models\AgentTarget;
use Liberu\CRM\ProspectingAgent\Services\ProspectingAgentPolicy;

final class SelectTarget
{
    public function __construct(private readonly ProspectingAgentPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): AgentTarget
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['run_id' => ['required', 'integer'], 'prospect_id' => ['required', 'integer'], 'research' => ['nullable', 'array']])->validate();
        AgentRun::query()->where('team_id', $teamId)->where('approved', true)->findOrFail($data['run_id']);

        return AgentTarget::query()->firstOrCreate(['team_id' => $teamId, 'run_id' => $data['run_id'], 'prospect_id' => $data['prospect_id']], ['team_id' => $teamId, ...$data]);
    }
}
