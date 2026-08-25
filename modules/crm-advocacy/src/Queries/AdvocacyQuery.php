<?php

declare(strict_types=1);

namespace Liberu\CRM\Advocacy\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Advocacy\Models\AdvocacyRecord;

final class AdvocacyQuery
{
    public function records(int $teamId, ?string $kind = null): Builder
    {
        return AdvocacyRecord::query()->forTeam($teamId)->when($kind, fn (Builder $query) => $query->where('kind', $kind))->latest();
    }
}
