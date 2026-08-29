<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Filament\Resources\PlaybookResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\PlaybooksAndEnablement\Actions\UpdatePlaybook;
use Liberu\CRM\PlaybooksAndEnablement\Filament\Resources\PlaybookResource;
use Liberu\CRM\PlaybooksAndEnablement\Models\Playbook;

final class EditPlaybook extends EditRecord
{
    protected static string $resource = PlaybookResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Playbook, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdatePlaybook::class)->execute((int) $teamId, auth()->id(), $record->id, $data);
    }
}
