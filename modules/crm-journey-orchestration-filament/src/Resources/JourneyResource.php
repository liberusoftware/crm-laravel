<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestrationFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\JourneyOrchestration\Models\Journey;
use Liberu\CRM\JourneyOrchestrationFilament\Resources\Pages\CreateJourney;
use Liberu\CRM\JourneyOrchestrationFilament\Resources\Pages\EditJourney;
use Liberu\CRM\JourneyOrchestrationFilament\Resources\Pages\ListJourneys;

final class JourneyResource extends Resource
{
    protected static ?string $model = Journey::class;

    protected static ?string $navigationLabel = 'Journeys';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListJourneys::route('/'), 'create' => CreateJourney::route('/create'), 'edit' => EditJourney::route('/{record}/edit')];
    }
}
