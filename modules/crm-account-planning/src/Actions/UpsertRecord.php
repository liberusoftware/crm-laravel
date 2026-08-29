<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanning\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\AccountPlanning\Models\AccountPlanningRecord;

final class UpsertRecord
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, array $attributes, ?int $id = null): AccountPlanningRecord
    {
        return DB::transaction(function () use ($teamId, $attributes, $id): AccountPlanningRecord {
            $record = $id === null ? new AccountPlanningRecord() : AccountPlanningRecord::query()->forTeam($teamId)->lockForUpdate()->findOrFail($id);
            $record->fill(['team_id' => $teamId, 'kind' => $attributes['kind'] ?? $record->kind, 'name' => $attributes['name'] ?? $record->name, 'status' => $attributes['status'] ?? $record->status ?? 'draft', 'account_id' => $attributes['account_id'] ?? $record->account_id, 'owner_id' => $attributes['owner_id'] ?? $record->owner_id, 'payload' => $attributes['payload'] ?? $record->payload ?? [], 'starts_at' => $attributes['starts_at'] ?? $record->starts_at, 'ends_at' => $attributes['ends_at'] ?? $record->ends_at])->save();

            return $record->refresh();
        });
    }
}
