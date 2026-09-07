<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CustomerDataModel\Filament\Resources\LayoutDefinitionResource\Pages\CreateLayoutDefinition;
use Liberu\CRM\CustomerDataModel\Filament\Resources\LayoutDefinitionResource\Pages\EditLayoutDefinition;
use Liberu\CRM\CustomerDataModel\Filament\Resources\LayoutDefinitionResource\Pages\ListLayoutDefinitions;
use Liberu\CRM\CustomerDataModel\Models\LayoutDefinition;
use Liberu\CRM\CustomerDataModel\Models\ObjectDefinition;

final class LayoutDefinitionResource extends Resource
{
    protected static ?string $model = LayoutDefinition::class;

    protected static ?string $navigationLabel = 'Object layouts';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('object_id')->label('Object')->options(fn (): array => ObjectDefinition::query()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'))->orderBy('label')->pluck('label', 'id')->all())->searchable()->required(),
            TextInput::make('key')->required()->alphaDash()->maxLength(80),
            TextInput::make('label')->required()->maxLength(255),
            KeyValue::make('sections')->json()->label('Layout sections')->required()->columnSpanFull(),
            Toggle::make('is_default')->label('Default layout'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('object.label')->label('Object')->searchable()->sortable(),
            TextColumn::make('key')->searchable()->sortable(),
            TextColumn::make('label')->searchable(),
            IconColumn::make('is_default')->boolean()->sortable(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->whereHas('object', fn (Builder $query): Builder => $query->where('team_id', $teamId));
    }

    public static function getPages(): array
    {
        return ['index' => ListLayoutDefinitions::route('/'), 'create' => CreateLayoutDefinition::route('/create'), 'edit' => EditLayoutDefinition::route('/{record}/edit')];
    }
}
