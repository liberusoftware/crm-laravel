<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboarding\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ClientOnboarding\Models\ClientOnboarding;

final class ClientOnboardingQuery
{
    public function onboardings(int $teamId): Builder
    {
        return ClientOnboarding::query()->where('team_id', $teamId)->latest();
    }
}
