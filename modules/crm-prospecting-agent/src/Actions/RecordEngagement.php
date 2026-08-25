<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ProspectingAgent\Models\AgentEngagement;
use Liberu\CRM\ProspectingAgent\Services\ProspectingAgentPolicy;

final class RecordEngagement
{
    public function __construct(private readonly ProspectingAgentPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): AgentEngagement
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['target_id' => ['required', 'integer'], 'event' => ['required', 'in:delivered,opened,clicked,replied,bounced'], 'payload' => ['nullable', 'array']])->validate();

        return AgentEngagement::query()->create(['team_id' => $teamId, ...$data, 'occurred_at' => now()]);
    }
}
