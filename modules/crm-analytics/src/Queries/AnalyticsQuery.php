<?php

declare(strict_types=1);

namespace Liberu\CRM\Analytics\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Analytics\Models\AnalyticsAsset;

final class AnalyticsQuery
{
    public function assets(int $teamId): Builder
    {
        return AnalyticsAsset::query()->where('team_id', $teamId)->latest();
    }
}
