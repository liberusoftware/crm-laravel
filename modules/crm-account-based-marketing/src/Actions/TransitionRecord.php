<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketing\Actions;

use Liberu\CRM\AccountBasedMarketing\Models\AccountBasedMarketingRecord;

final class TransitionRecord
{
    public function execute(int $teamId, int $id, string $status): AccountBasedMarketingRecord
    {
        $record = AccountBasedMarketingRecord::query()->forTeam($teamId)->findOrFail($id);
        $record->transitionTo($status);

        return $record->refresh();
    }
}
