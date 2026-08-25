<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Filament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ResourcePlanning\Filament\Resources\PlanningResource\Pages\ListBookings;
use Liberu\CRM\ResourcePlanning\Models\ResourceBooking;

final class PlanningResource extends Resource
{
    protected static ?string $model = ResourceBooking::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('resource_id')->numeric()->required(), TextInput::make('subject_type')->required(), TextInput::make('subject_id')->numeric()->required(), DateTimePicker::make('starts_at')->required(), DateTimePicker::make('ends_at')->required(), Select::make('status')->options(['tentative' => 'Tentative', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled'])->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('resource_id'), TextColumn::make('subject_type'), TextColumn::make('subject_id'), TextColumn::make('starts_at')->dateTime(), TextColumn::make('ends_at')->dateTime(), TextColumn::make('status')->badge(), TextColumn::make('hours')]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListBookings::route('/')];
    }
}
