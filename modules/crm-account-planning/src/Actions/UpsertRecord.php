<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanning\Actions;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Liberu\CRM\AccountPlanning\Models\AccountPlanningRecord;

final class UpsertRecord
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, array $attributes, ?int $id = null): AccountPlanningRecord
    {
        if ($teamId < 1) {
            throw new InvalidArgumentException('A valid team is required.');
        }

        $kind = $attributes['kind'] ?? null;
        $name = isset($attributes['name']) ? trim((string) $attributes['name']) : null;

        if ($id === null && (! is_string($kind) || ! in_array($kind, AccountPlanningRecord::KINDS, true))) {
            throw new InvalidArgumentException('The account planning kind is not supported.');
        }

        $status = $attributes['status'] ?? null;

        if ($status !== null && (! is_string($status) || ! in_array($status, AccountPlanningRecord::STATUSES, true))) {
            throw new InvalidArgumentException('The account planning status is not supported.');
        }

        if ($name !== null && $name === '') {
            throw new InvalidArgumentException('A record name is required.');
        }

        return DB::transaction(function () use ($teamId, $attributes, $id): AccountPlanningRecord {
            $record = $id === null ? new AccountPlanningRecord() : AccountPlanningRecord::query()->forTeam($teamId)->lockForUpdate()->findOrFail($id);
            $record->fill(['team_id' => $teamId, 'kind' => $attributes['kind'] ?? $record->kind, 'name' => isset($attributes['name']) ? trim((string) $attributes['name']) : $record->name, 'status' => $attributes['status'] ?? $record->status ?? 'draft', 'account_id' => $attributes['account_id'] ?? $record->account_id, 'owner_id' => $attributes['owner_id'] ?? $record->owner_id, 'payload' => $attributes['payload'] ?? $record->payload ?? [], 'starts_at' => $attributes['starts_at'] ?? $record->starts_at, 'ends_at' => $attributes['ends_at'] ?? $record->ends_at])->save();

            return $record->refresh();
        });
    }
}
