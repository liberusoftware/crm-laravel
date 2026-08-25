<?php

declare(strict_types=1);

namespace Liberu\CRM\Advertising\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\Advertising\Models\AdvertisingRecord;

final class UpsertRecord
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, array $attributes, ?int $id = null): AdvertisingRecord
    {
        return DB::transaction(function () use ($teamId, $attributes, $id): AdvertisingRecord {
            $record = $id === null ? new AdvertisingRecord() : AdvertisingRecord::query()->forTeam($teamId)->lockForUpdate()->findOrFail($id);
            $record->fill(['team_id' => $teamId, 'kind' => $attributes['kind'] ?? $record->kind, 'name' => $attributes['name'] ?? $record->name, 'status' => $attributes['status'] ?? $record->status ?? 'draft', 'external_id' => $attributes['external_id'] ?? $record->external_id, 'platform' => $attributes['platform'] ?? $record->platform, 'payload' => $attributes['payload'] ?? $record->payload ?? [], 'starts_at' => $attributes['starts_at'] ?? $record->starts_at, 'ends_at' => $attributes['ends_at'] ?? $record->ends_at])->save();

            return $record->refresh();
        });
    }
}
