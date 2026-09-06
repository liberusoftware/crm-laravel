<?php

declare(strict_types=1);

namespace Liberu\CRM\ContractsFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Contracts\Models\Contract;
use Liberu\CRM\ContractsFilament\Resources\ContractResource\Pages\CreateContractPage;
use Liberu\CRM\ContractsFilament\Resources\ContractResource\Pages\ListContracts;

final class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(180), KeyValue::make('parties')->json()->required(), KeyValue::make('terms')->json()->required(), KeyValue::make('clauses')->json(), KeyValue::make('obligations')->json(), DatePicker::make('starts_on'), DatePicker::make('ends_on'), DatePicker::make('renewal_on')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('version'), TextColumn::make('starts_on')->date(), TextColumn::make('ends_on')->date()->sortable(), TextColumn::make('renewal_on')->date()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListContracts::route('/'), 'create' => CreateContractPage::route('/create')];
    }
}
