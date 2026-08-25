<?php

declare(strict_types=1);

namespace Liberu\CRM\Copilot\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Copilot\Models\CopilotAction;
use Liberu\CRM\Copilot\Models\CopilotRequest;
use Liberu\CRM\Copilot\Services\CopilotPolicy;

final class ProposeCopilotAction
{
    public function __construct(private readonly CopilotPolicy $policy) {}

    public function execute(int $teamId, int $userId, CopilotRequest $request, array $input): CopilotAction
    {
        abort_unless($request->team_id === $teamId && $request->user_id === $userId && $this->policy->canUse($teamId, $userId), 403);
        $data = Validator::make($input, ['action' => ['required', 'in:update_record,create_task,send_draft'], 'payload' => ['required', 'array']])->validate();

        return CopilotAction::query()->create(['team_id' => $teamId, 'request_id' => $request->id, 'user_id' => $userId, 'status' => 'pending_confirmation', ...$data]);
    }
}
