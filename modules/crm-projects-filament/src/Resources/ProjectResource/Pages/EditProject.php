<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Filament\Resources\ProjectResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Projects\Actions\UpdateProject;
use Liberu\CRM\Projects\Filament\Resources\ProjectResource;
use Liberu\CRM\Projects\Models\Project;

final class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Project, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdateProject::class)->execute((int) $teamId, auth()->id(), $record->id, $data);
    }
}
