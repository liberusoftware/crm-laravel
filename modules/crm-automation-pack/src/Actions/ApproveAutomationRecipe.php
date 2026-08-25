<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPack\Actions;

use Liberu\CRM\AutomationPack\Models\AutomationApproval;
use Liberu\CRM\AutomationPack\Models\AutomationRecipe;
use Liberu\CRM\AutomationPack\Services\AutomationPackPolicy;

final class ApproveAutomationRecipe
{
    public function __construct(private readonly AutomationPackPolicy $policy) {}

    public function execute(int $teamId, int $userId, AutomationRecipe $recipe, string $decision, ?string $reason = null): AutomationApproval
    {
        abort_unless($recipe->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        abort_unless(in_array($decision, ['approved', 'rejected'], true), 422);
        $approval = AutomationApproval::query()->create(['team_id' => $teamId, 'recipe_id' => $recipe->id, 'actor_id' => $userId, 'status' => $decision, 'reason' => $reason, 'decided_at' => now()]);
        if ($decision === 'approved') {
            $recipe->update(['status' => 'active']);
        } if ($decision === 'rejected') {
            $recipe->update(['status' => 'draft']);
        }

        return $approval;
    }
}
