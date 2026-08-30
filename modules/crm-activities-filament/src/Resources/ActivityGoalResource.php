<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\CRM\Activities\Filament\Resources\ActivityGoalResource\Pages\CreateActivityGoal;
use Liberu\CRM\Activities\Filament\Resources\ActivityGoalResource\Pages\EditActivityGoal;
use Liberu\CRM\Activities\Filament\Resources\ActivityGoalResource\Pages\ListActivityGoals;
use Liberu\CRM\Activities\Models\ActivityGoal;

final class ActivityGoalResource extends Resource
{
    protected static ?string $model = ActivityGoal::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(160), TextInput::make('kind')->required()->maxLength(60), TextInput::make('target')->numeric()->required(), TextInput::make('starts_at')->required(), TextInput::make('ends_at')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('kind')->badge(), TextColumn::make('progress'), TextColumn::make('target'), TextColumn::make('status')->badge()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListActivityGoals::route('/'), 'create' => CreateActivityGoal::route('/create'), 'edit' => EditActivityGoal::route('/{record}/edit')];
    }
}
