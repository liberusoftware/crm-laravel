<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformanceFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\GoalsAndPerformance\Models\PerformanceGoal;
use Liberu\CRM\GoalsAndPerformanceFilament\Resources\Pages\CreatePerformanceGoal;
use Liberu\CRM\GoalsAndPerformanceFilament\Resources\Pages\EditPerformanceGoal;
use Liberu\CRM\GoalsAndPerformanceFilament\Resources\Pages\ListPerformanceGoals;

final class PerformanceGoalResource extends Resource
{
    protected static ?string $model = PerformanceGoal::class;

    protected static ?string $navigationLabel = 'Performance';

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
        return ['index' => ListPerformanceGoals::route('/'), 'create' => CreatePerformanceGoal::route('/create'), 'edit' => EditPerformanceGoal::route('/{record}/edit')];
    }
}
