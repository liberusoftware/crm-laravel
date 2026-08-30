<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\Core\Models\Record;

final class CreateRecord
{
    /** @param array<string, mixed> $data */
    public function execute(string $type, int $teamId, string $name, array $data = [], ?int $ownerId = null): Record
    {
        return DB::transaction(function () use ($type, $teamId, $name, $data, $ownerId): Record {
            $record = Record::query()->create([
                'record_type' => $type,
                'team_id' => $teamId,
                'owner_id' => $ownerId,
                'name' => $name,
                'data' => $data,
            ]);

            $record->timeline()->create([
                'team_id' => $teamId,
                'actor_id' => auth()->id(),
                'event_type' => 'record.created',
                'summary' => 'Record created',
                'payload' => ['record_type' => $type],
            ]);

            return $record;
        });
    }
}
