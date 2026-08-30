<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordinationFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\FieldServiceCoordination\Models\ServiceAppointment;

final class ServiceAppointmentResource extends Resource
{
    protected static ?string $model = ServiceAppointment::class;

    protected static ?string $navigationLabel = 'Field appointments';

    public static function getPages(): array
    {
        return [];
    }
}
