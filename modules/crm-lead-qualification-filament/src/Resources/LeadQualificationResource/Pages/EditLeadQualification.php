<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Filament\Resources\LeadQualificationResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\LeadQualification\Actions\UpdateScores;
use Liberu\CRM\LeadQualification\Filament\Resources\LeadQualificationResource;
use Liberu\CRM\LeadQualification\Models\LeadQualification;

final class EditLeadQualification extends EditRecord
{
    protected static string $resource = LeadQualificationResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof LeadQualification, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdateScores::class)->execute($record, auth()->id(), $data);
    }
}
