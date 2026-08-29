<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Telephony\Filament\Resources\TelephonyCallResource\Pages\ListTelephonyCalls;
use Liberu\CRM\Telephony\Models\TelephonyCall;

final class TelephonyCallResource extends Resource
{
    protected static ?string $model = TelephonyCall::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('from_number'), TextColumn::make('to_number'), TextColumn::make('direction')->badge(), TextColumn::make('status')->badge(), TextColumn::make('disposition'), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListTelephonyCalls::route('/')];
    }
}
