<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Liberu\CRM\WorkManagement\Models\WorkItem;

final class WorkAudit
{
    /** @param array<string, mixed> $details */
    public function record(Model $item, ?int $actorId, string $event, array $details = []): void
    {
        DB::table('crm_work_audits')->insert(['team_id' => (int) $item->getAttribute('team_id'), 'work_item_id' => $item->getAttribute('work_item_id') ?? ($item instanceof WorkItem ? $item->getKey() : null), 'actor_id' => $actorId, 'event' => $event, 'details' => json_encode($details, JSON_THROW_ON_ERROR), 'created_at' => now(), 'updated_at' => now()]);
    }
}
