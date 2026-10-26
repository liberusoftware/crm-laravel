<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordinationFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\FieldServiceCoordination\Models\ServiceAppointment;
use Liberu\CRM\FieldServiceCoordinationFilament\Resources\Pages\CreateServiceAppointment;
use Liberu\CRM\FieldServiceCoordinationFilament\Resources\Pages\EditServiceAppointment;
use Liberu\CRM\FieldServiceCoordinationFilament\Resources\Pages\ListServiceAppointments;

final class ServiceAppointmentResource extends Resource
{
    protected static ?string $model = ServiceAppointment::class;

    protected static ?string $navigationLabel = 'Field appointments';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListServiceAppointments::route('/'), 'create' => CreateServiceAppointment::route('/create'), 'edit' => EditServiceAppointment::route('/{record}/edit')];
    }
}
