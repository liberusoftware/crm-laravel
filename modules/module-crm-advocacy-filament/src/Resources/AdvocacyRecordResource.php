<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvocacyFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Advocacy\Models\AdvocacyRecord;
use Liberu\CRM\AdvocacyFilament\Resources\Pages\CreateAdvocacyRecord;
use Liberu\CRM\AdvocacyFilament\Resources\Pages\EditAdvocacyRecord;
use Liberu\CRM\AdvocacyFilament\Resources\Pages\ListAdvocacyRecords;

final class AdvocacyRecordResource extends Resource
{
    protected static ?string $model = AdvocacyRecord::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('kind')->options(array_combine(AdvocacyRecord::KINDS, AdvocacyRecord::KINDS))->required(),
            TextInput::make('name')->required()->maxLength(255),
            Select::make('status')->options(array_combine(AdvocacyRecord::STATUSES, AdvocacyRecord::STATUSES))->required(),
            TextInput::make('contact_id')->numeric()->minValue(1),
            KeyValue::make('payload'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind')->badge(), TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListAdvocacyRecords::route('/'), 'create' => CreateAdvocacyRecord::route('/create'), 'edit' => EditAdvocacyRecord::route('/{record}/edit')];
    }
}
