<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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

        if (isset($attributes['status']) && ! in_array($attributes['status'], ['active', 'archived'], true)) {
            throw new \InvalidArgumentException('The record status is not supported.');
        }

        return DB::transaction(function () use ($record, $attributes): Record {
            if (array_key_exists('owner_id', $attributes) && $attributes['owner_id'] !== null) {
                $ownerId = (int) $attributes['owner_id'];
                $belongsToTeam = DB::table('team_user')
                    ->where('team_id', $record->team_id)
                    ->where('user_id', $ownerId)
                    ->exists()
                    || DB::table('teams')
                        ->where('id', $record->team_id)
                        ->where('user_id', $ownerId)
                        ->exists();

                if (! $belongsToTeam) {
                    throw ValidationException::withMessages(['owner_id' => 'Owner must belong to this team.']);
                }
            }

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
