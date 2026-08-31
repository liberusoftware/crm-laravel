<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketing\Actions;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Liberu\CRM\AccountBasedMarketing\Models\AccountBasedMarketingRecord;

final class UpsertRecord
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, array $attributes, ?int $id = null): AccountBasedMarketingRecord
    {
        if ($teamId < 1) {
            throw new InvalidArgumentException('A valid team is required.');
        }

        $kind = $attributes['kind'] ?? null;
        $name = isset($attributes['name']) ? trim((string) $attributes['name']) : null;
        $status = $attributes['status'] ?? null;

        if ($id === null && (! is_string($kind) || ! in_array($kind, AccountBasedMarketingRecord::KINDS, true))) {
            throw new InvalidArgumentException('The account-based marketing kind is not supported.');
        }
        if ($name !== null && $name === '') {
            throw new InvalidArgumentException('A record name is required.');
        }
        if ($status !== null && (! is_string($status) || ! in_array($status, AccountBasedMarketingRecord::STATUSES, true))) {
            throw new InvalidArgumentException('The account-based marketing status is not supported.');
        }

        return DB::transaction(function () use ($teamId, $attributes, $id, $name): AccountBasedMarketingRecord {
            $record = $id === null
                ? new AccountBasedMarketingRecord()
                : AccountBasedMarketingRecord::query()->forTeam($teamId)->lockForUpdate()->findOrFail($id);

            $record->fill([
                'team_id' => $teamId,
                'kind' => $attributes['kind'] ?? $record->kind,
                'name' => $name ?? $record->name,
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
