<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Filament\Resources\WorkItemResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\WorkManagement\Actions\CreateWorkItem as CreateWorkItemAction;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkItemResource;

final class CreateWorkItem extends CreateRecord
{
    protected static string $resource = WorkItemResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return app(CreateWorkItemAction::class)->execute((int) $teamId, auth()->id(), $data);
    }
}
