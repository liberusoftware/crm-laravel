<?php

declare(strict_types=1);

namespace Liberu\CRM\CommunitiesFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Communities\Models\CommunitySpace;
use Liberu\CRM\CommunitiesFilament\Resources\CommunitySpaceResource\Pages\CreateCommunitySpacePage;
use Liberu\CRM\CommunitiesFilament\Resources\CommunitySpaceResource\Pages\ListCommunitySpaces;

final class CommunitySpaceResource extends Resource
{
    protected static ?string $model = CommunitySpace::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(180), Select::make('kind')->options(['customer' => 'Customer', 'partner' => 'Partner', 'internal' => 'Internal'])->required(), KeyValue::make('settings')->json()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('kind')->badge(), TextColumn::make('status')->badge(), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListCommunitySpaces::route('/'), 'create' => CreateCommunitySpacePage::route('/create')];
    }
}
