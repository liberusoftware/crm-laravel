<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ProspectingAgent\Models\AgentRun;
use Liberu\CRM\ProspectingAgent\Services\ProspectingAgentPolicy;

final class CreateAgentRun
{
    public function __construct(private readonly ProspectingAgentPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): AgentRun
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'targeting' => ['required', 'array'], 'policy' => ['required', 'array']])->validate();
        abort_unless(($data['policy']['requires_approval'] ?? true) === true, 422, 'Agent runs must require approval.');

        return AgentRun::query()->create(['team_id' => $teamId, ...$data]);
    }
}
