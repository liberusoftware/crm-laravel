<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Prospecting\Filament\Resources\ProspectResource\Pages\ListProspects;
use Liberu\CRM\Prospecting\Models\Prospect;

final class ProspectResource extends Resource
{
    protected static ?string $model = Prospect::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('provider')->required(), TextInput::make('provider_id'), TextInput::make('name')->required(), TextInput::make('company'), TextInput::make('email')->email(), Select::make('status')->options(['new' => 'New', 'researched' => 'Researched', 'contacted' => 'Contacted'])]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name'), TextColumn::make('company'), TextColumn::make('email'), TextColumn::make('provider'), TextColumn::make('status')->badge()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListProspects::route('/')];
    }
}
