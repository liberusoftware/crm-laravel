<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\CRM\DataOperations\Filament\Resources\DataOperationResource\Pages\CreateDataOperation;
use Liberu\CRM\DataOperations\Filament\Resources\DataOperationResource\Pages\EditDataOperation;
use Liberu\CRM\DataOperations\Filament\Resources\DataOperationResource\Pages\ListDataOperations;
use Liberu\CRM\DataOperations\Models\DataOperation;

final class DataOperationResource extends Resource
{
    protected static ?string $model = DataOperation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('kind')->options(['import' => 'Import', 'export' => 'Export', 'enrichment' => 'Enrichment', 'deduplication' => 'Deduplication', 'formatting' => 'Formatting', 'quality' => 'Quality'])->required(), TextInput::make('source')->maxLength(255), TextInput::make('target')->maxLength(255), Textarea::make('failure_reason')->disabled()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind')->badge(), TextColumn::make('status')->badge(), TextColumn::make('source')->searchable(), TextColumn::make('processed_rows')->label('Processed'), TextColumn::make('failed_rows')->label('Failed'), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDataOperations::route('/'), 'create' => CreateDataOperation::route('/create'), 'edit' => EditDataOperation::route('/{record}/edit')];
    }
}
