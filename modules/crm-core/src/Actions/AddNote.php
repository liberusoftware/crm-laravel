<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\Core\Models\Note;
use Liberu\CRM\Core\Models\Record;

final class AddNote
{
    public function execute(Record $record, string $body, ?int $authorId = null): Note
    {
        return DB::transaction(function () use ($record, $body, $authorId): Note {
            $note = $record->notes()->create([
                'team_id' => $record->team_id,
                'author_id' => $authorId ?? auth()->id(),
                'body' => trim($body),
            ]);
            $record->timeline()->create([
                'team_id' => $record->team_id,
                'actor_id' => $authorId ?? auth()->id(),
                'event_type' => 'note.added',
                'summary' => 'Note added',
            ]);

            return $note;
        });
    }
}
