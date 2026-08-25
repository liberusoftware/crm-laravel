<?php

declare(strict_types=1);

namespace Liberu\CRM\Advocacy\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\Advocacy\Models\AdvocacyRecord;

final class UpsertRecord
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, array $attributes, ?int $id = null): AdvocacyRecord
    {
        return DB::transaction(function () use ($teamId, $attributes, $id): AdvocacyRecord {
            $record = $id === null ? new AdvocacyRecord() : AdvocacyRecord::query()->forTeam($teamId)->lockForUpdate()->findOrFail($id);
            $record->fill(['team_id' => $teamId, 'kind' => $attributes['kind'] ?? $record->kind, 'name' => $attributes['name'] ?? $record->name, 'status' => $attributes['status'] ?? $record->status ?? 'draft', 'contact_id' => $attributes['contact_id'] ?? $record->contact_id, 'owner_id' => $attributes['owner_id'] ?? $record->owner_id, 'payload' => $attributes['payload'] ?? $record->payload ?? [], 'requested_at' => $attributes['requested_at'] ?? $record->requested_at])->save();

            return $record->refresh();
        });
    }
}
