<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordinationFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\FieldServiceCoordinationFilament\Resources\ServiceAppointmentResource;

final class CreateServiceAppointment extends CreateRecord
{
    protected static string $resource = ServiceAppointmentResource::class;
}
