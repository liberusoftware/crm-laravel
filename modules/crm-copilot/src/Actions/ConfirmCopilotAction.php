<?php

declare(strict_types=1);

namespace Liberu\CRM\Copilot\Actions;

use Liberu\CRM\Copilot\Models\CopilotAction;
use Liberu\CRM\Copilot\Services\CopilotPolicy;

final class ConfirmCopilotAction
{
    public function __construct(private readonly CopilotPolicy $policy) {}

    public function execute(int $teamId, int $userId, CopilotAction $action): CopilotAction
    {
        abort_unless($action->team_id === $teamId && $action->user_id === $userId && $this->policy->canUse($teamId, $userId), 403);
        abort_unless($action->status === 'pending_confirmation', 422);
        $action->update(['status' => 'confirmed', 'confirmed_at' => now()]);

        return $action->refresh();
    }
}
