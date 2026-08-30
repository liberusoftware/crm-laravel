<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspace\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\AgencyWorkspace\Models\AgencyAccount;

final class AgencyQuery
{
    public function accounts(int $teamId): Builder
    {
        return AgencyAccount::query()->where('team_id', $teamId)->with('access')->latest();
    }
}
