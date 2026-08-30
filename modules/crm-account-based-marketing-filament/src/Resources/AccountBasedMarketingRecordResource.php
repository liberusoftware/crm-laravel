<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketingFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\CRM\AccountBasedMarketing\Models\AccountBasedMarketingRecord;

final class AccountBasedMarketingRecordResource extends Resource
{
    protected static ?string $model = AccountBasedMarketingRecord::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('kind')->options(array_combine(AccountBasedMarketingRecord::KINDS, AccountBasedMarketingRecord::KINDS))->required(),
            TextInput::make('name')->required()->maxLength(255),
            Select::make('status')->options(array_combine(AccountBasedMarketingRecord::STATUSES, AccountBasedMarketingRecord::STATUSES))->required(),
            TextInput::make('account_id')->numeric()->minValue(1),
            KeyValue::make('payload')->keyLabel('Field')->valueLabel('Value'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('kind')->badge()->sortable(),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [];
    }
}
