<?php

declare(strict_types=1);

namespace Liberu\CRM\Advertising\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Advertising\Models\AdvertisingRecord;

final class AdvertisingQuery
{
    public function records(int $teamId, ?string $kind = null): Builder
    {
        return AdvertisingRecord::query()->forTeam($teamId)->when($kind, fn (Builder $query) => $query->where('kind', $kind))->latest();
    }
}
