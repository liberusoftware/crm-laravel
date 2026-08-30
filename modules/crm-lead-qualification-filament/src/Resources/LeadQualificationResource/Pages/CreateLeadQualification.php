<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Filament\Resources\LeadQualificationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\LeadQualification\Actions\CreateQualification;
use Liberu\CRM\LeadQualification\Filament\Resources\LeadQualificationResource;

final class CreateLeadQualification extends CreateRecord
{
    protected static string $resource = LeadQualificationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateQualification::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
