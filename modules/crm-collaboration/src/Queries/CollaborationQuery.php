<?php

declare(strict_types=1);

namespace Liberu\CRM\Collaboration\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Collaboration\Models\CollaborationRecord;
use Liberu\CRM\Collaboration\Models\CollaborationWork;

final class CollaborationQuery
{
    public function records(int $teamId, string $recordKey): Builder
    {
        return CollaborationRecord::query()->where('team_id', $teamId)->where('record_key', $recordKey)->latest();
    }

    public function queue(int $teamId, string $queueKey): Builder
    {
        return CollaborationWork::query()->where('team_id', $teamId)->where('queue_key', $queueKey)->where('status', 'open')->latest();
    }
}
