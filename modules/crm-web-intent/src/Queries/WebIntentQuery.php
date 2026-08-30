<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\WebIntent\Models\WebIntentAlert;
use Liberu\CRM\WebIntent\Models\WebIntentVisit;

final class WebIntentQuery
{
    /** @return Builder<WebIntentVisit> */
    public function visits(int $teamId): Builder
    {
        return WebIntentVisit::query()->where('team_id', $teamId);
    }

    /** @return Builder<WebIntentAlert> */
    public function alerts(int $teamId): Builder
    {
        return WebIntentAlert::query()->where('team_id', $teamId);
    }

    /** @return array{total:int,hot:int,warm:int,open_alerts:int} */
    public function summary(int $teamId): array
    {
        $visits = $this->visits($teamId);

        return ['total' => (clone $visits)->count(), 'hot' => (clone $visits)->where('intent_level', 'hot')->count(), 'warm' => (clone $visits)->where('intent_level', 'warm')->count(), 'open_alerts' => $this->alerts($teamId)->where('status', 'open')->count()];
    }
}
