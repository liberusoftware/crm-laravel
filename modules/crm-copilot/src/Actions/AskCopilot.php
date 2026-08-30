<?php

declare(strict_types=1);

namespace Liberu\CRM\Copilot\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Copilot\Contracts\AutomationGateway;
use Liberu\CRM\Copilot\Models\CopilotRequest;
use Liberu\CRM\Copilot\Services\CopilotPolicy;

final class AskCopilot
{
    public function __construct(private readonly CopilotPolicy $policy, private readonly AutomationGateway $gateway) {}

    public function execute(int $teamId, int $userId, array $input): CopilotRequest
    {
        abort_unless($this->policy->canUse($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:search,summary,preparation,draft,update,task'], 'input' => ['required', 'string', 'max:10000'], 'context' => ['nullable', 'array']])->validate();
        $request = CopilotRequest::query()->create(['team_id' => $teamId, 'user_id' => $userId, 'status' => 'completed', ...$data]);
        $request->update(['result' => $this->gateway->complete($data['input'], $data['context'] ?? [])]);

        return $request->refresh();
    }
}
