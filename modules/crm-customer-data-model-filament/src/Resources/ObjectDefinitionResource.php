<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\CRM\CustomerDataModel\Filament\Resources\ObjectDefinitionResource\Pages\CreateObjectDefinition;
use Liberu\CRM\CustomerDataModel\Filament\Resources\ObjectDefinitionResource\Pages\EditObjectDefinition;
use Liberu\CRM\CustomerDataModel\Filament\Resources\ObjectDefinitionResource\Pages\ListObjectDefinitions;
use Liberu\CRM\CustomerDataModel\Models\ObjectDefinition;

final class ObjectDefinitionResource extends Resource
{
    protected static ?string $model = ObjectDefinition::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required()->alphaDash()->maxLength(80), TextInput::make('label')->required()->maxLength(255), Textarea::make('description')->maxLength(10000)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable(), TextColumn::make('label')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('current_version')->label('Version'), TextColumn::make('updated_at')->dateTime()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListObjectDefinitions::route('/'), 'create' => CreateObjectDefinition::route('/create'), 'edit' => EditObjectDefinition::route('/{record}/edit')];
    }
}
