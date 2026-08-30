<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatform\Queries;

use Liberu\CRM\CustomerDataPlatform\Models\CdpAudience;
use Liberu\CRM\CustomerDataPlatform\Models\CdpProfile;

final class CdpQuery
{
    public function profiles(int $teamId)
    {
        return CdpProfile::query()->where('team_id', $teamId)->latest();
    }

    public function audiences(int $teamId)
    {
        return CdpAudience::query()->where('team_id', $teamId)->latest();
    }
}
