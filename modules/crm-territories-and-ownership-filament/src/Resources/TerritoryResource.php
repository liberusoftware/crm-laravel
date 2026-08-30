<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\TerritoriesAndOwnership\Filament\Resources\TerritoryResource\Pages\ListTerritories;
use Liberu\CRM\TerritoriesAndOwnership\Models\TerritoryRule;

final class TerritoryResource extends Resource
{
    protected static ?string $model = TerritoryRule::class;

    protected static ?string $slug = 'territory-rules';

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('name')->required(), TextInput::make('book_of_business'), TextInput::make('capacity')->numeric()]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('name'), TextColumn::make('book_of_business'), TextColumn::make('capacity'), TextColumn::make('active')]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function mutateFormDataBeforeCreate(array $d): array
    {
        $d['team_id'] = (int) auth()->user()->current_team_id;
        $d['members'] = [];

        return $d;
    }

    public static function getPages(): array
    {
        return ['index' => ListTerritories::route('/')];
    }
}
