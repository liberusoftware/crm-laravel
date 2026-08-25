<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Scheduling\Filament\Resources\BookingResource\Pages\ListBookings;
use Liberu\CRM\Scheduling\Models\Booking;

final class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('invitee_name')->required(), TextInput::make('invitee_email')->email()->required(), TextInput::make('starts_at')->required(), Select::make('status')->options(['confirmed' => 'Confirmed', 'cancelled' => 'Cancelled', 'no_show' => 'No show'])->required()]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('invitee_name')->searchable(), TextColumn::make('invitee_email'), TextColumn::make('starts_at')->dateTime(), TextColumn::make('status')->badge()]);
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
