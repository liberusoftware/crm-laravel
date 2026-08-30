<?php

declare(strict_types=1);

namespace Liberu\CRM\Loyalty\Queries;

use Liberu\CRM\Loyalty\Models\LoyaltyLedgerEntry;
use Liberu\CRM\Loyalty\Models\LoyaltyMember;

final class LoyaltyQuery
{
    public function forTeam(int $teamId)
    {
        return LoyaltyMember::query()->where('team_id', $teamId)->latest();
    }

    public function statement(int $teamId, int $memberId)
    {
        return LoyaltyLedgerEntry::query()->where('team_id', $teamId)->where('member_id', $memberId)->latest();
    }
}
