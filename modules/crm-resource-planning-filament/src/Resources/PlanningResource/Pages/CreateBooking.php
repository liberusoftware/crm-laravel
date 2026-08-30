<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Filament\Resources\PlanningResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\ResourcePlanning\Actions\CreateBooking as CreateBookingAction;
use Liberu\CRM\ResourcePlanning\Filament\Resources\PlanningResource;

final class CreateBooking extends CreateRecord
{
    protected static string $resource = PlanningResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateBookingAction::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
