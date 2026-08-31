<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Filament\Resources;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Core\Enums\RecordType;
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
            Hidden::make('team_id')->default(fn (): ?int => auth()->user()?->current_team_id),
            Select::make('record_type')->options(collect(RecordType::cases())->mapWithKeys(fn (RecordType $type): array => [$type->value => ucfirst($type->value)])->all())->required()->disabledOn('edit'),
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('status')->required()->maxLength(30),
            KeyValue::make('data')->json(),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403, 'A current team is required.');

        return parent::getEloquentQuery()->where('team_id', (int) $teamId);
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
