<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Filament\Resources\ProspectResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Prospecting\Actions\UpdateProspect;
use Liberu\CRM\Prospecting\Filament\Resources\ProspectResource;
use Liberu\CRM\Prospecting\Models\Prospect;

final class EditProspect extends EditRecord
{
    protected static string $resource = ProspectResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Prospect, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdateProspect::class)->execute((int) $teamId, auth()->id(), $record->id, $data);
    }
}
