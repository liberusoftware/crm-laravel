<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Filament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\CRM\Core\Filament\Resources\RecordResource\Pages\CreateRecord;
use Liberu\CRM\Core\Filament\Resources\RecordResource\Pages\EditRecord;
use Liberu\CRM\Core\Filament\Resources\RecordResource\Pages\ListRecords;
use Liberu\CRM\Core\Models\Record;

final class RecordResource extends Resource
{
    protected static ?string $model = Record::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('record_type')->required()->maxLength(40),
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('status')->required()->maxLength(30),
            KeyValue::make('data')->json(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('record_type')->badge()->sortable(),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('status')->badge(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListRecords::route('/'), 'create' => CreateRecord::route('/create'), 'edit' => EditRecord::route('/{record}/edit')];
    }
}
