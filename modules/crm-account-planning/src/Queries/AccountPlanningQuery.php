<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanning\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\AccountPlanning\Models\AccountPlanningRecord;

final class AccountPlanningQuery
{
    public function records(int $teamId, ?string $kind = null): Builder
    {
        return AccountPlanningRecord::query()->forTeam($teamId)->when($kind, fn (Builder $query) => $query->where('kind', $kind))->latest();
    }
}
