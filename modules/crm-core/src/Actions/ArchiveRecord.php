<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\Core\Models\Record;

final class ArchiveRecord
{
    public function execute(Record $record): Record
    {
        return DB::transaction(function () use ($record): Record {
            $record->archive();
            $record->timeline()->create([
                'team_id' => $record->team_id,
                'actor_id' => auth()->id(),
                'event_type' => 'record.archived',
                'summary' => 'Record archived',
            ]);

            return $record->refresh();
        });
    }
}
