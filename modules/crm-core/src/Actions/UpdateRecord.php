<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\Core\Models\Record;

final class UpdateRecord
{
    /** @param array{name?: string, status?: string, data?: array<string, mixed>, owner_id?: int|null} $attributes */
    public function execute(Record $record, array $attributes): Record
    {
        if (array_key_exists('name', $attributes)) {
            $attributes['name'] = trim((string) $attributes['name']);
        }

        if (($attributes['name'] ?? $record->name) === '') {
            throw new \InvalidArgumentException('A record name is required.');
        }

        return DB::transaction(function () use ($record, $attributes): Record {
            $record->fill($attributes);
            $record->save();
            $record->timeline()->create([
                'team_id' => $record->team_id,
                'actor_id' => auth()->id(),
                'event_type' => 'record.updated',
                'summary' => 'Record updated',
                'payload' => ['fields' => array_keys($attributes)],
            ]);

            return $record->refresh();
        });
    }
}
