<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgent\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\MarketingAgent\Models\AgentCheck;
use Liberu\CRM\MarketingAgent\Models\AgentRequest;
use Liberu\CRM\MarketingAgent\Services\MarketingAgentPolicy;

final class RecordAgentCheck
{
    public function __construct(private readonly MarketingAgentPolicy $policy) {}

    public function execute(int $teamId, int $userId, AgentRequest $request, array $input): AgentCheck
    {
        abort_unless($request->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:brand,consent,approval'], 'status' => ['required', 'in:pending,passed,failed,approved,rejected'], 'details' => ['nullable', 'string']])->validate();

        return AgentCheck::query()->create(['team_id' => $teamId, 'request_id' => $request->id, ...$data]);
    }
}
