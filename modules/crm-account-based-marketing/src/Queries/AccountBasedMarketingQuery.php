<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketing\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\AccountBasedMarketing\Models\AccountBasedMarketingRecord;

final class AccountBasedMarketingQuery
{
    public function records(int $teamId, ?string $kind = null): Builder
    {
        return AccountBasedMarketingRecord::query()
            ->forTeam($teamId)
            ->when($kind, fn (Builder $query) => $query->where('kind', $kind))
            ->latest();
    }
}
