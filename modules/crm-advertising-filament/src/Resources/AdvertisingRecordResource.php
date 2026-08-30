<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvertisingFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\CRM\Advertising\Models\AdvertisingRecord;

final class AdvertisingRecordResource extends Resource
{
    protected static ?string $model = AdvertisingRecord::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('kind')->options(array_combine(AdvertisingRecord::KINDS, AdvertisingRecord::KINDS))->required(),
            TextInput::make('name')->required()->maxLength(255),
            Select::make('status')->options(array_combine(AdvertisingRecord::STATUSES, AdvertisingRecord::STATUSES))->required(),
            TextInput::make('platform')->maxLength(48),
            TextInput::make('external_id')->maxLength(255),
            KeyValue::make('payload'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind')->badge(), TextColumn::make('name')->searchable(), TextColumn::make('platform')->badge(), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [];
    }
}
