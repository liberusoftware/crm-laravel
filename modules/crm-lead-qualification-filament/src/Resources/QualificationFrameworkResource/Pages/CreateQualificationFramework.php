<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Filament\Resources\QualificationFrameworkResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\LeadQualification\Actions\CreateFramework;
use Liberu\CRM\LeadQualification\Filament\Resources\QualificationFrameworkResource;

final class CreateQualificationFramework extends CreateRecord
{
    protected static string $resource = QualificationFrameworkResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return app(CreateFramework::class)->execute((int) $teamId, auth()->id(), $data);
    }
}
