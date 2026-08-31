<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CPQ\Models\CpqQuote;
use Liberu\CRM\CPQFilament\Resources\CpqQuoteResource\Pages\CreateCpqQuote;
use Liberu\CRM\CPQFilament\Resources\CpqQuoteResource\Pages\EditCpqQuote;
use Liberu\CRM\CPQFilament\Resources\CpqQuoteResource\Pages\ListCpqQuotes;

final class CpqQuoteResource extends Resource
{
    protected static ?string $model = CpqQuote::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('currency')->required()->length(3)->default('USD'),
            KeyValue::make('configuration'),
            Repeater::make('lines')->schema([
                TextInput::make('description')->required()->maxLength(255),
                TextInput::make('unit_price')->numeric()->minValue(0)->required(),
                TextInput::make('quantity')->numeric()->gt(0)->required(),
                TextInput::make('discount')->numeric()->minValue(0)->default(0),
            ])->minItems(1)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('status')->badge(),
            TextColumn::make('currency'),
            TextColumn::make('total')->numeric(decimalPlaces: 2),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListCpqQuotes::route('/'), 'create' => CreateCpqQuote::route('/create'), 'edit' => EditCpqQuote::route('/{record}/edit')];
    }
}
