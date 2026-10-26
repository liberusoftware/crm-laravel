<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordinationFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\FieldServiceCoordinationFilament\Resources\ServiceAppointmentResource;

final class EditServiceAppointment extends EditRecord
{
    protected static string $resource = ServiceAppointmentResource::class;
}
