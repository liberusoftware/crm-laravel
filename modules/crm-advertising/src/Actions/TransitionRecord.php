<?php

declare(strict_types=1);

namespace Liberu\CRM\Advertising\Actions;

use Liberu\CRM\Advertising\Models\AdvertisingRecord;

final class TransitionRecord
{
    public function execute(int $teamId, int $id, string $status): AdvertisingRecord
    {
        $record = AdvertisingRecord::query()->forTeam($teamId)->findOrFail($id);
        $record->transitionTo($status);

        return $record->refresh();
    }
}
