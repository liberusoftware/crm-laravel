<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketing\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\AccountBasedMarketing\Models\AccountBasedMarketingRecord;

final class UpsertRecord
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, array $attributes, ?int $id = null): AccountBasedMarketingRecord
    {
        return DB::transaction(function () use ($teamId, $attributes, $id): AccountBasedMarketingRecord {
            $record = $id === null
                ? new AccountBasedMarketingRecord()
                : AccountBasedMarketingRecord::query()->forTeam($teamId)->lockForUpdate()->findOrFail($id);

            $record->fill([
                'team_id' => $teamId,
                'kind' => $attributes['kind'] ?? $record->kind,
                'name' => $attributes['name'] ?? $record->name,
                'status' => $attributes['status'] ?? $record->status ?? 'draft',
                'account_id' => $attributes['account_id'] ?? $record->account_id,
                'owner_id' => $attributes['owner_id'] ?? $record->owner_id,
                'payload' => $attributes['payload'] ?? $record->payload ?? [],
                'starts_at' => $attributes['starts_at'] ?? $record->starts_at,
                'ends_at' => $attributes['ends_at'] ?? $record->ends_at,
            ]);
            $record->save();

            return $record->refresh();
        });
    }
}
