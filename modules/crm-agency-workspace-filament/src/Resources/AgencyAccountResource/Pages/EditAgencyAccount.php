<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspaceFilament\Resources\AgencyAccountResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\AgencyWorkspace\Models\AgencyAccount;
use Liberu\CRM\AgencyWorkspaceFilament\Resources\AgencyAccountResource;

final class EditAgencyAccount extends EditRecord
{
    protected static string $resource = AgencyAccountResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof AgencyAccount, 404);
        $teamId = auth()->user()?->getAttribute('current_team_id');
        abort_unless(is_numeric($teamId) && (int) $teamId === (int) $record->team_id, 403);
        $record->update($data);

        return $record->refresh();
    }
}
