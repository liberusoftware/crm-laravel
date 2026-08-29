<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Telephony\Filament\Resources\TelephonyQueueResource\Pages\ListTelephonyQueues;
use Liberu\CRM\Telephony\Models\TelephonyQueue;

final class TelephonyQueueResource extends Resource
{
    protected static ?string $model = TelephonyQueue::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('name')->required(), Select::make('strategy')->options(['ring_all' => 'Ring all', 'round_robin' => 'Round robin', 'least_calls' => 'Least calls'])->required(), TextInput::make('max_wait_seconds')->numeric()->required(), Toggle::make('active')]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('name')->searchable(), TextColumn::make('strategy'), TextColumn::make('max_wait_seconds'), TextColumn::make('active')->badge()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListTelephonyQueues::route('/')];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()->current_team_id;

        return $data;
    }
}
