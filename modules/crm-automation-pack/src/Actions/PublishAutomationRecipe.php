<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPack\Actions;

use Liberu\CRM\AutomationPack\Models\AutomationRecipe;
use Liberu\CRM\AutomationPack\Services\AutomationPackPolicy;

final class PublishAutomationRecipe
{
    public function __construct(private readonly AutomationPackPolicy $policy) {}

    public function execute(int $teamId, int $userId, AutomationRecipe $recipe): AutomationRecipe
    {
        abort_unless($recipe->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        abort_unless($recipe->status === 'draft', 422);
        $recipe->update(['status' => $recipe->approval_required ? 'pending_approval' : 'active', 'version' => $recipe->version + 1]);

        return $recipe->refresh();
    }
}
