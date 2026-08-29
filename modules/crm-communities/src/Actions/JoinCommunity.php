<?php

declare(strict_types=1);

namespace Liberu\CRM\Communities\Actions;

use Liberu\CRM\Communities\Models\CommunityMembership;
use Liberu\CRM\Communities\Models\CommunitySpace;

final class JoinCommunity
{
    public function execute(int $teamId, CommunitySpace $space, string $subjectKey, string $role = 'member'): CommunityMembership
    {
        abort_unless((int) $space->team_id === $teamId && $space->status === 'active' && $subjectKey !== '' && in_array($role, ['member', 'moderator', 'admin'], true), 422);

        return CommunityMembership::query()->updateOrCreate(['space_id' => $space->id, 'subject_key' => $subjectKey], ['team_id' => $teamId, 'role' => $role, 'status' => 'active']);
    }
}
