<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPack\Actions;

use Liberu\CRM\AutomationPack\Models\AutomationRecipe;
use Liberu\CRM\AutomationPack\Models\AutomationRun;
use Liberu\CRM\AutomationPack\Services\AutomationPackPolicy;

final class SimulateAutomationRecipe
{
    public function __construct(private readonly AutomationPackPolicy $policy) {}

    public function execute(int $teamId, int $userId, AutomationRecipe $recipe, array $input): AutomationRun
    {
        abort_unless($recipe->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);

        return AutomationRun::query()->create(['team_id' => $teamId, 'recipe_id' => $recipe->id, 'actor_id' => $userId, 'kind' => 'simulation', 'status' => 'completed', 'input' => $input, 'output' => ['triggers' => $recipe->triggers, 'actions' => $recipe->actions]]);
    }
}
