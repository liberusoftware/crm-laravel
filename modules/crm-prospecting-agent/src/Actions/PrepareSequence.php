<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ProspectingAgent\Models\AgentSequence;
use Liberu\CRM\ProspectingAgent\Models\AgentTarget;
use Liberu\CRM\ProspectingAgent\Services\ProspectingAgentPolicy;

final class PrepareSequence
{
    public function __construct(private readonly ProspectingAgentPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): AgentSequence
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['run_id' => ['required', 'integer'], 'target_id' => ['required', 'integer'], 'step' => ['required', 'integer', 'min:1'], 'channel' => ['required', 'in:email,linkedin,sms'], 'content' => ['required', 'string', 'max:10000']])->validate();
        AgentTarget::query()->where('team_id', $teamId)->where('run_id', $data['run_id'])->findOrFail($data['target_id']);

        return AgentSequence::query()->create(['team_id' => $teamId, ...$data, 'status' => 'prepared']);
    }
}
