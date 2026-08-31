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
        if ($teamId < 1) {
            throw new \InvalidArgumentException('A valid team is required.');
        }

        $kind = $attributes['kind'] ?? null;
        if ($id === null && (! is_string($kind) || ! in_array($kind, AdvertisingRecord::KINDS, true))) {
            throw new \InvalidArgumentException('Unsupported advertising kind.');
        }

        $name = array_key_exists('name', $attributes) ? trim((string) $attributes['name']) : null;
        if ($id === null && ($name === null || $name === '')) {
            throw new \InvalidArgumentException('A non-empty advertising name is required.');
        }

        return DB::transaction(function () use ($teamId, $attributes, $id): AdvertisingRecord {
            $record = $id === null ? new AdvertisingRecord() : AdvertisingRecord::query()->forTeam($teamId)->lockForUpdate()->findOrFail($id);
            $record->fill(['team_id' => $teamId, 'kind' => $attributes['kind'] ?? $record->kind, 'name' => $attributes['name'] ?? $record->name, 'status' => $attributes['status'] ?? $record->status ?? 'draft', 'external_id' => $attributes['external_id'] ?? $record->external_id, 'platform' => $attributes['platform'] ?? $record->platform, 'payload' => $attributes['payload'] ?? $record->payload ?? [], 'starts_at' => $attributes['starts_at'] ?? $record->starts_at, 'ends_at' => $attributes['ends_at'] ?? $record->ends_at])->save();

            return $record->refresh();
        });
    }
}
