<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQ\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CPQ\Models\CpqQuote;

final class CpqQuery
{
    public function quotes(int $teamId): Builder
    {
        return CpqQuote::query()->where('team_id', $teamId)->latest();
    }
}
