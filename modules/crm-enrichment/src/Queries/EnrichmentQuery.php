<?php

declare(strict_types=1);

namespace Liberu\CRM\Enrichment\Queries;

use Liberu\CRM\Enrichment\Models\EnrichmentChange;
use Liberu\CRM\Enrichment\Models\EnrichmentProfile;

final class EnrichmentQuery
{
    public function profiles(int $teamId)
    {
        return EnrichmentProfile::query()->where('team_id', $teamId)->latest();
    }

    public function changes(int $teamId, int $profileId)
    {
        return EnrichmentChange::query()->where('team_id', $teamId)->where('profile_id', $profileId)->latest('detected_at');
    }
}
