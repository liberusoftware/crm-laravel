<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Filament\Resources\WorkItemResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\WorkManagement\Actions\UpdateWorkItem;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkItemResource;
use Liberu\CRM\WorkManagement\Models\WorkItem;

final class EditWorkItem extends EditRecord
{
    protected static string $resource = WorkItemResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof WorkItem, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdateWorkItem::class)->execute($record, auth()->id(), $data, $record->version);
    }
}
