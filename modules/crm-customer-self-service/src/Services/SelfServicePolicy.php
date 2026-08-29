<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfService\Services;

use App\Models\Team;

final class SelfServicePolicy
{
    public function canAccess(int $teamId, int $userId): bool
    {
        $team = Team::query()->find($teamId);

        return $team !== null && ($team->users()->whereKey($userId)->exists() || (int) $team->user_id === $userId);
    }
}
