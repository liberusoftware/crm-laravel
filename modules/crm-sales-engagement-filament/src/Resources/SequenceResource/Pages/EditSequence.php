<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\SalesEngagement\Actions\UpdateSequence;
use Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource;
use Liberu\CRM\SalesEngagement\Models\EngagementSequence;

final class EditSequence extends EditRecord
{
    protected static string $resource = SequenceResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof EngagementSequence, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdateSequence::class)->execute((int) $teamId, auth()->id(), $record->id, $data);
    }
}
