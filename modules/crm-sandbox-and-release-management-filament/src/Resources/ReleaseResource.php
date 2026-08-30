<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\SandboxAndReleaseManagement\Filament\Resources\ReleaseResource\Pages\ListReleases;
use Liberu\CRM\SandboxAndReleaseManagement\Models\ReleaseChangeset;

final class ReleaseResource extends Resource
{
    protected static ?string $model = ReleaseChangeset::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('name')->required(), Select::make('status')->options(['draft' => 'Draft', 'validated' => 'Validated', 'promoted' => 'Promoted', 'rolled_back' => 'Rolled back']), Select::make('source_environment')->options(['sandbox' => 'Sandbox', 'staging' => 'Staging', 'production' => 'Production'])->required(), Select::make('target_environment')->options(['sandbox' => 'Sandbox', 'staging' => 'Staging', 'production' => 'Production'])->required()]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('source_environment'), TextColumn::make('target_environment'), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListReleases::route('/')];
    }
}
