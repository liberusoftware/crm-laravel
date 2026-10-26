<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordinationFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\FieldServiceCoordinationFilament\Resources\ServiceAppointmentResource;

final class ListServiceAppointments extends ListRecords
{
    protected static string $resource = ServiceAppointmentResource::class;
}
