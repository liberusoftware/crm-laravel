<?php

declare(strict_types=1);

namespace Liberu\CRM\Contracts\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Liberu\CRM\Contracts\Models\Contract;

final class ContractQuery
{
    public function contracts(int $teamId): Builder
    {
        return Contract::query()->where('team_id', $teamId)->latest();
    }

    public function complianceDates(int $teamId): Collection
    {
        return Contract::query()->where('team_id', $teamId)->whereNotNull('ends_on')->orderBy('ends_on')->get();
    }
}
