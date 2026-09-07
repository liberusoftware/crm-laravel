<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Liberu\CRM\Core\Enums\RecordType;
use Liberu\CRM\Core\Models\Record;

final class CreateRecord
{
    /** @param array<string, mixed> $data */
    public function execute(string $type, int $teamId, string $name, array $data = [], ?int $ownerId = null): Record
    {
        $type = strtolower(trim($type));
        $name = trim($name);

        if (RecordType::tryFrom($type) === null) {
            throw new InvalidArgumentException('The record type is not supported.');
        }

        if ($teamId < 1 || $name === '') {
            throw new InvalidArgumentException('A team and record name are required.');
        }

        return DB::transaction(function () use ($type, $teamId, $name, $data, $ownerId): Record {
            $this->ensureOwnerBelongsToTeam($teamId, $ownerId);
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

    private function ensureOwnerBelongsToTeam(int $teamId, ?int $ownerId): void
    {
        if ($ownerId === null) {
            return;
        }

        $belongsToTeam = DB::table('team_user')
            ->where('team_id', $teamId)
            ->where('user_id', $ownerId)
            ->exists()
            || DB::table('teams')
                ->where('id', $teamId)
                ->where('user_id', $ownerId)
                ->exists();

        if (! $belongsToTeam) {
            throw ValidationException::withMessages(['owner_id' => 'Owner must belong to this team.']);
        }
    }
}
