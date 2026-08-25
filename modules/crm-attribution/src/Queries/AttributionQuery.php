<?php

declare(strict_types=1);

namespace Liberu\CRM\Attribution\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Attribution\Models\Conversion;
use Liberu\CRM\Attribution\Models\Touchpoint;

final class AttributionQuery
{
    public function touchpoints(int $teamId): Builder
    {
        return Touchpoint::query()->where('team_id', $teamId)->latest('occurred_at');
    }

    public function conversions(int $teamId): Builder
    {
        return Conversion::query()->where('team_id', $teamId)->latest('converted_at');
    }
}
