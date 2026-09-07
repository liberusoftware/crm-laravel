<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
        return $schema->components([
            TextInput::make('key')->required()->alphaDash()->maxLength(80)->helperText('Stable identifier used by integrations and APIs.'),
            TextInput::make('label')->required()->maxLength(255),
            Textarea::make('description')->maxLength(10000)->columnSpanFull(),
            Select::make('status')->options(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived'])->default('draft')->required(),
            Toggle::make('is_standard')->label('Standard object')->helperText('Standard objects are managed by the platform.')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('key')->searchable()->sortable(),
            TextColumn::make('label')->searchable()->sortable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('current_version')->label('Version')->sortable(),
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
        return ['index' => ListObjectDefinitions::route('/'), 'create' => CreateObjectDefinition::route('/create'), 'edit' => EditObjectDefinition::route('/{record}/edit')];
    }
}
