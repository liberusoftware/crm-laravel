<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPack\Queries;

use Liberu\CRM\AutomationPack\Models\AutomationRecipe;

final class AutomationPackQuery
{
    public function recipes(int $teamId)
    {
        return AutomationRecipe::query()->where('team_id', $teamId)->latest();
    }
}
