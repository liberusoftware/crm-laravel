<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\CRM\TerritoriesAndOwnership\Models\OwnershipHistory;
use Liberu\CRM\TerritoriesAndOwnership\Models\TerritoryRule;

final class TerritoryQuery
{
    public function rules(int $teamId): LengthAwarePaginator
    {
        return TerritoryRule::query()->where('team_id', $teamId)->latest()->paginate(25);
    }

    public function history(int $teamId): LengthAwarePaginator
    {
        return OwnershipHistory::query()->where('team_id', $teamId)->latest()->paginate(25);
    }
}
