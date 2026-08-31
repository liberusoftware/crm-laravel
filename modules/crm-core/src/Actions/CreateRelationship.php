<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Actions;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Liberu\CRM\Core\Models\Record;
use Liberu\CRM\Core\Models\Relationship;

final class CreateRelationship
{
    /** @param array<string, mixed> $metadata */
    public function execute(Record $from, Record $to, string $type, array $metadata = []): Relationship
    {
        $type = trim($type);
        if ($from->team_id !== $to->team_id || $type === '') {
            throw new InvalidArgumentException('Related records must belong to the same team and have a relationship type.');
        }

        return DB::transaction(function () use ($from, $to, $type, $metadata): Relationship {
            $relationship = new Relationship();
            $relationship->forceFill([
                'team_id' => $from->team_id,
                'from_type' => $from::class,
                'from_id' => $from->getKey(),
                'to_type' => $to::class,
                'to_id' => $to->getKey(),
                'relationship_type' => $type,
                'metadata' => $metadata,
            ])->save();

            $from->timeline()->create([
                'team_id' => $from->team_id,
                'actor_id' => auth()->id(),
                'event_type' => 'record.relationship.created',
                'summary' => 'Relationship created',
                'payload' => ['to_id' => $to->getKey(), 'relationship_type' => $type],
            ]);

            return $relationship;
        });
    }
}
