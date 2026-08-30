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
use Liberu\CRM\Telephony\Filament\Resources\TelephonyNumberResource\Pages\CreateTelephonyNumber;
use Liberu\CRM\Telephony\Filament\Resources\TelephonyNumberResource\Pages\ListTelephonyNumbers;
use Liberu\CRM\Telephony\Models\TelephonyNumber;

final class TelephonyNumberResource extends Resource
{
    protected static ?string $model = TelephonyNumber::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('number')->required()->maxLength(32), TextInput::make('label')->maxLength(255), Select::make('provider')->options(['twilio' => 'Twilio', 'zernio' => 'Zernio'])->required(), Select::make('status')->options(['active' => 'Active', 'inactive' => 'Inactive'])->required(), Toggle::make('caller_id_enabled')]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('number')->searchable(), TextColumn::make('label'), TextColumn::make('provider')->badge(), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListTelephonyNumbers::route('/'), 'create' => CreateTelephonyNumber::route('/create')];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()->current_team_id;

        return $data;
    }
}
