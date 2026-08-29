<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Queries;

use Liberu\CRM\SandboxAndReleaseManagement\Models\ReleaseChangeset;
use Liberu\CRM\SandboxAndReleaseManagement\Models\ReleaseDeployment;
use Liberu\CRM\SandboxAndReleaseManagement\Models\ReleaseSnapshot;

final class ReleaseQuery
{
    public function snapshots(int $teamId)
    {
        return ReleaseSnapshot::query()->where('team_id', $teamId)->latest();
    }

    public function changesets(int $teamId)
    {
        return ReleaseChangeset::query()->where('team_id', $teamId)->latest();
    }

    public function deployments(int $teamId)
    {
        return ReleaseDeployment::query()->where('team_id', $teamId)->latest();
    }
}
