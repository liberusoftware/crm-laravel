<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanning\Actions;

use Liberu\CRM\AccountPlanning\Models\AccountPlanningRecord;

final class TransitionRecord
{
    public function execute(int $teamId, int $id, string $status): AccountPlanningRecord
    {
        $record = AccountPlanningRecord::query()->forTeam($teamId)->findOrFail($id);
        $record->transitionTo($status);

        return $record->refresh();
    }
}
