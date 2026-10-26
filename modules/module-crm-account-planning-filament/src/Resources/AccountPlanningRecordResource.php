<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanningFilament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\AccountPlanning\Models\AccountPlanningRecord;
use Liberu\CRM\AccountPlanningFilament\Resources\AccountPlanningRecordResource\Pages\CreateAccountPlanningRecord;
use Liberu\CRM\AccountPlanningFilament\Resources\AccountPlanningRecordResource\Pages\EditAccountPlanningRecord;
use Liberu\CRM\AccountPlanningFilament\Resources\AccountPlanningRecordResource\Pages\ListAccountPlanningRecords;

final class AccountPlanningRecordResource extends Resource
{
    protected static ?string $model = AccountPlanningRecord::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('kind')->options(array_combine(AccountPlanningRecord::KINDS, AccountPlanningRecord::KINDS))->required(),
            TextInput::make('name')->required()->maxLength(255),
            Select::make('status')->options(array_combine(AccountPlanningRecord::STATUSES, AccountPlanningRecord::STATUSES))->required(),
            TextInput::make('account_id')->numeric()->minValue(1),
            TextInput::make('owner_id')->numeric()->minValue(1),
            DateTimePicker::make('starts_at'),
            DateTimePicker::make('ends_at'),
            KeyValue::make('payload'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind')->badge(), TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListAccountPlanningRecords::route('/'), 'create' => CreateAccountPlanningRecord::route('/create'), 'edit' => EditAccountPlanningRecord::route('/{record}/edit')];
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }
}
