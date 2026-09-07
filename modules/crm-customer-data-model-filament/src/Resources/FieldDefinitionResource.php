<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CustomerDataModel\Filament\Resources\FieldDefinitionResource\Pages\CreateFieldDefinition;
use Liberu\CRM\CustomerDataModel\Filament\Resources\FieldDefinitionResource\Pages\EditFieldDefinition;
use Liberu\CRM\CustomerDataModel\Filament\Resources\FieldDefinitionResource\Pages\ListFieldDefinitions;
use Liberu\CRM\CustomerDataModel\Models\FieldDefinition;
use Liberu\CRM\CustomerDataModel\Models\ObjectDefinition;

final class FieldDefinitionResource extends Resource
{
    protected static ?string $model = FieldDefinition::class;

    protected static ?string $navigationLabel = 'Custom fields';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('object_id')->label('Object')->options(fn (): array => ObjectDefinition::query()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'))->orderBy('label')->pluck('label', 'id')->all())->searchable()->required(),
            TextInput::make('key')->required()->alphaDash()->maxLength(80),
            TextInput::make('label')->required()->maxLength(255),
            Select::make('type')->options(['text' => 'Text', 'long_text' => 'Long text', 'number' => 'Number', 'boolean' => 'Boolean', 'date' => 'Date', 'datetime' => 'Date and time', 'select' => 'Select', 'multi_select' => 'Multi-select', 'currency' => 'Currency', 'email' => 'Email', 'url' => 'URL'])->required(),
            Textarea::make('description')->maxLength(10000)->columnSpanFull(),
            KeyValue::make('config')->json()->label('Field configuration')->columnSpanFull(),
            Toggle::make('is_required')->label('Required field'),
            Toggle::make('is_calculated')->label('Calculated field'),
            Textarea::make('calculation')->label('Calculation expression')->visible(fn ($get): bool => (bool) $get('is_calculated'))->columnSpanFull(),
            KeyValue::make('required_stages')->json()->label('Required at stages')->columnSpanFull(),
            TextInput::make('position')->numeric()->minValue(0)->default(0)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('object.label')->label('Object')->searchable()->sortable(),
            TextColumn::make('key')->searchable()->sortable(),
            TextColumn::make('label')->searchable(),
            TextColumn::make('type')->badge(),
            IconColumn::make('is_required')->boolean(),
            IconColumn::make('is_calculated')->boolean(),
            TextColumn::make('position')->sortable(),
        ])->defaultSort('position');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->whereHas('object', fn (Builder $query): Builder => $query->where('team_id', $teamId));
    }

    public static function getPages(): array
    {
        return ['index' => ListFieldDefinitions::route('/'), 'create' => CreateFieldDefinition::route('/create'), 'edit' => EditFieldDefinition::route('/{record}/edit')];
    }
}
