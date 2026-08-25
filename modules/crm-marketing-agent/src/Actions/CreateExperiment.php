<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgent\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\MarketingAgent\Models\AgentExperiment;
use Liberu\CRM\MarketingAgent\Models\AgentRequest;
use Liberu\CRM\MarketingAgent\Services\MarketingAgentPolicy;

final class CreateExperiment
{
    public function __construct(private readonly MarketingAgentPolicy $policy) {}

    public function execute(int $teamId, int $userId, AgentRequest $request, array $input): AgentExperiment
    {
        abort_unless($request->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'variants' => ['required', 'array', 'min:2'], 'metrics' => ['nullable', 'array']])->validate();

        return AgentExperiment::query()->create(['team_id' => $teamId, 'request_id' => $request->id, ...$data]);
    }
}
