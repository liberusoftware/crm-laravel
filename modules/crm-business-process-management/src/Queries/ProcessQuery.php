<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagement\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\BusinessProcessManagement\Models\Process;
use Liberu\CRM\BusinessProcessManagement\Models\ProcessRun;

final class ProcessQuery
{
    public function processes(int $teamId): Builder
    {
        return Process::query()->where('team_id', $teamId)->latest();
    }

    public function runs(int $teamId): Builder
    {
        return ProcessRun::query()->where('team_id', $teamId)->latest();
    }
}
