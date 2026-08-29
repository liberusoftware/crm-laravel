<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Queries;

use Liberu\CRM\Referrals\Models\Referral;
use Liberu\CRM\Referrals\Models\ReferralProgram;
use Liberu\CRM\Referrals\Models\ReferralReward;

final class ReferralQuery
{
    public function programs(int $teamId)
    {
        return ReferralProgram::query()->where('team_id', $teamId)->latest();
    }

    public function referrals(int $teamId)
    {
        return Referral::query()->where('team_id', $teamId)->latest();
    }

    public function rewards(int $teamId)
    {
        return ReferralReward::query()->where('team_id', $teamId)->latest();
    }
}
