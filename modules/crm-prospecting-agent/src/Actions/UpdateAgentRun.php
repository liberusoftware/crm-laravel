<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ProspectingAgent\Models\AgentRun;
use Liberu\CRM\ProspectingAgent\Services\ProspectingAgentPolicy;

final class UpdateAgentRun
{
    public function __construct(private readonly ProspectingAgentPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $runId, array $input): AgentRun
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'targeting' => ['required', 'array'],
            'policy' => ['required', 'array'],
        ])->validate();
        abort_unless(($data['policy']['requires_approval'] ?? true) === true, 422, 'Agent runs must require approval.');
        $run = AgentRun::query()->where('team_id', $teamId)->findOrFail($runId);
        $run->update($data);

        return $run->refresh();
    }
}
