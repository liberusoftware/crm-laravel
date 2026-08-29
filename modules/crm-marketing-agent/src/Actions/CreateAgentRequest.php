<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgent\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\MarketingAgent\Models\AgentRequest;
use Liberu\CRM\MarketingAgent\Services\MarketingAgentPolicy;

final class CreateAgentRequest
{
    public function __construct(private readonly MarketingAgentPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): AgentRequest
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:audience,campaign,content,variation,journey'], 'brief' => ['required', 'string', 'max:100000'], 'audience' => ['nullable', 'array'], 'result' => ['nullable', 'array'], 'metadata' => ['nullable', 'array']])->validate();

        return AgentRequest::query()->create(['team_id' => $teamId, 'actor_id' => $userId, ...$data]);
    }
}
