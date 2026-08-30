<?php

declare(strict_types=1);

namespace Liberu\CRM\Advocacy\Actions;

use Liberu\CRM\Advocacy\Models\AdvocacyRecord;

final class TransitionRecord
{
    public function execute(int $teamId, int $id, string $status): AdvocacyRecord
    {
        $record = AdvocacyRecord::query()->forTeam($teamId)->findOrFail($id);
        $record->transitionTo($status);

        return $record->refresh();
    }
}
