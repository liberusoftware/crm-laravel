<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CustomerDataModel\Filament\Resources\RelationshipDefinitionResource\Pages\CreateRelationshipDefinition;
use Liberu\CRM\CustomerDataModel\Filament\Resources\RelationshipDefinitionResource\Pages\EditRelationshipDefinition;
use Liberu\CRM\CustomerDataModel\Filament\Resources\RelationshipDefinitionResource\Pages\ListRelationshipDefinitions;
use Liberu\CRM\CustomerDataModel\Models\ObjectDefinition;
use Liberu\CRM\CustomerDataModel\Models\RelationshipDefinition;

final class RelationshipDefinitionResource extends Resource
{
    protected static ?string $model = RelationshipDefinition::class;

    protected static ?string $navigationLabel = 'Object relationships';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('from_object_id')->label('From object')->options(fn (): array => self::objectOptions())->searchable()->required(),
            Select::make('to_object_id')->label('To object')->options(fn (): array => self::objectOptions())->searchable()->required()->different('from_object_id'),
            TextInput::make('key')->required()->alphaDash()->maxLength(80)->helperText('Stable association key used by integrations.'),
            TextInput::make('label')->required()->maxLength(255),
            Select::make('cardinality')->options(['one_to_one' => 'One to one', 'one_to_many' => 'One to many', 'many_to_one' => 'Many to one', 'many_to_many' => 'Many to many'])->default('many_to_many')->required(),
            KeyValue::make('config')->json()->label('Relationship configuration')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('fromObject.label')->label('From')->searchable()->sortable(),
            TextColumn::make('toObject.label')->label('To')->searchable()->sortable(),
            TextColumn::make('key')->searchable(),
            TextColumn::make('label')->searchable(),
            TextColumn::make('cardinality')->badge(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListRelationshipDefinitions::route('/'), 'create' => CreateRelationshipDefinition::route('/create'), 'edit' => EditRelationshipDefinition::route('/{record}/edit')];
    }

    /** @return array<int|string, string> */
    private static function objectOptions(): array
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');

        return ObjectDefinition::query()->where('team_id', $teamId)->orderBy('label')->pluck('label', 'id')->all();
    }
}
