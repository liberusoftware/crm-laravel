<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagement\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\AffiliateManagement\Models\Affiliate;
use Liberu\CRM\AffiliateManagement\Models\AffiliateEvent;

final class AffiliateQuery
{
    public function affiliates(int $teamId): Builder
    {
        return Affiliate::query()->where('team_id', $teamId)->with('links')->latest();
    }

    public function events(int $teamId): Builder
    {
        return AffiliateEvent::query()->where('team_id', $teamId)->latest();
    }
}
