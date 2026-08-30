<?php

declare(strict_types=1);

namespace Liberu\CRM\Communities\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Communities\Models\CommunityContent;
use Liberu\CRM\Communities\Models\CommunitySpace;

final class CommunityQuery
{
    public function spaces(int $teamId): Builder
    {
        return CommunitySpace::query()->where('team_id', $teamId)->where('status', 'active')->latest();
    }

    public function feed(int $teamId, int $spaceId): Builder
    {
        return CommunityContent::query()->where('team_id', $teamId)->where('space_id', $spaceId)->where('status', 'published')->latest();
    }
}
