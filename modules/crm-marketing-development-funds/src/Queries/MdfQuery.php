<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFunds\Queries;

use Liberu\CRM\MarketingDevelopmentFunds\Models\MdfFund;

final class MdfQuery
{
    public function forTeam(int $teamId)
    {
        return MdfFund::query()->where('team_id', $teamId)->latest();
    }
}
