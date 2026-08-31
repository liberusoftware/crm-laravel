<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinarsFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\EventsAndWebinars\Models\CrmEvent;
use Liberu\CRM\EventsAndWebinarsFilament\Resources\Pages\CreateCrmEvent;
use Liberu\CRM\EventsAndWebinarsFilament\Resources\Pages\EditCrmEvent;
use Liberu\CRM\EventsAndWebinarsFilament\Resources\Pages\ListCrmEvents;

final class CrmEventResource extends Resource
{
    protected static ?string $model = CrmEvent::class;

    protected static ?string $navigationLabel = 'Events and webinars';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListCrmEvents::route('/'), 'create' => CreateCrmEvent::route('/create'), 'edit' => EditCrmEvent::route('/{record}/edit')];
    }
}
