<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagement\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CaseManagement\Models\CaseRecord;

final class CaseQuery
{
    public function cases(int $teamId): Builder
    {
        return CaseRecord::query()->where('team_id', $teamId)->latest();
    }

    public function queue(int $teamId, string $status = 'open'): Builder
    {
        return CaseRecord::query()->where('team_id', $teamId)->where('status', $status)->orderByDesc('priority');
    }
}
