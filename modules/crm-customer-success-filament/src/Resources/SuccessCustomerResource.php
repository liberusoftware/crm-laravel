<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccessFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CustomerSuccess\Models\SuccessCustomer;
use Liberu\CRM\CustomerSuccessFilament\Resources\Pages\CreateSuccessCustomer;
use Liberu\CRM\CustomerSuccessFilament\Resources\Pages\EditSuccessCustomer;
use Liberu\CRM\CustomerSuccessFilament\Resources\Pages\ListSuccessCustomers;

final class SuccessCustomerResource extends Resource
{
    protected static ?string $model = SuccessCustomer::class;

    protected static ?string $navigationLabel = 'Customer success';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('customer_key')->label('Customer key')->required()->maxLength(160),
            Select::make('segment')->options(['enterprise' => 'Enterprise', 'mid_market' => 'Mid-market', 'smb' => 'SMB', 'strategic' => 'Strategic'])->searchable(),
            Select::make('lifecycle')->options(['onboarding' => 'Onboarding', 'adopted' => 'Adopted', 'at_risk' => 'At risk', 'renewal' => 'Renewal', 'expanded' => 'Expanded', 'churned' => 'Churned'])->default('onboarding')->required(),
            TextInput::make('health_score')->numeric()->minValue(0)->maxValue(100)->default(50)->required()->helperText('Overall customer health from 0 to 100.'),
            KeyValue::make('onboarding')->json()->label('Onboarding plan')->columnSpanFull(),
            KeyValue::make('success_plan')->json()->label('Success plan')->columnSpanFull(),
            KeyValue::make('objectives')->json()->label('Customer objectives')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('customer_key')->label('Customer')->searchable()->sortable(),
            TextColumn::make('segment')->badge(),
            TextColumn::make('lifecycle')->badge(),
            TextColumn::make('health_score')->label('Health')->numeric()->sortable()->color(fn (int $state): string => $state < 40 ? 'danger' : ($state < 70 ? 'warning' : 'success')),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->defaultSort('health_score', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListSuccessCustomers::route('/'), 'create' => CreateSuccessCustomer::route('/create'), 'edit' => EditSuccessCustomer::route('/{record}/edit')];
    }
}
