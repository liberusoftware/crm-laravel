<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Filament\Resources\ProjectResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Projects\Actions\CreateProject as CreateProjectAction;
use Liberu\CRM\Projects\Filament\Resources\ProjectResource;

final class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateProjectAction::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
