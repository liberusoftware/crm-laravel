<?php

declare(strict_types=1);

namespace Liberu\CRM\Memberships\Queries;

use Liberu\CRM\Memberships\Models\MembershipGrant;
use Liberu\CRM\Memberships\Models\MembershipPlan;

final class MembershipQuery
{
    public function forTeam(int $teamId)
    {
        return MembershipPlan::query()->where('team_id', $teamId)->latest();
    }

    public function memberGrants(int $teamId, int $memberId)
    {
        return MembershipGrant::query()->where('team_id', $teamId)->where('member_id', $memberId)->where('status', 'active')->latest();
    }
}
