<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspaceFilament\Resources\AgencyAccountResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\AgencyWorkspace\Actions\CreateAgencyAccount;
use Liberu\CRM\AgencyWorkspaceFilament\Resources\AgencyAccountResource;

final class CreateAgencyAccountPage extends CreateRecord
{
    protected static string $resource = AgencyAccountResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->getAttribute('current_team_id');
        abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403);

        return app(CreateAgencyAccount::class)->execute((int) $teamId, (int) auth()->id(), $data);
    }
}
