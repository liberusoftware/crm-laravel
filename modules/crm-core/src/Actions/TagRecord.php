<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\Core\Models\Record;
use Liberu\CRM\Core\Models\Tag;

final class TagRecord
{
    public function execute(Record $record, Tag $tag): void
    {
        abort_unless($record->team_id === $tag->team_id, 404);

        DB::transaction(function () use ($record, $tag): void {
            $record->tags()->syncWithoutDetaching([$tag->getKey()]);
            $record->timeline()->create([
                'team_id' => $record->team_id,
                'actor_id' => auth()->id(),
                'event_type' => 'record.tagged',
                'summary' => 'Tag added',
                'payload' => ['tag_id' => $tag->getKey()],
            ]);
        });
    }
}
