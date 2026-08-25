<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\SlaAndEntitlements\Filament\Resources\SlaContractResource\Pages\CreateSlaContract;
use Liberu\CRM\SlaAndEntitlements\Filament\Resources\SlaContractResource\Pages\ListSlaContracts;
use Liberu\CRM\SlaAndEntitlements\Models\SlaContract;

final class SlaContractResource extends Resource
{
    protected static ?string $model = SlaContract::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('name')->required(), Select::make('status')->options(['draft' => 'Draft', 'active' => 'Active', 'suspended' => 'Suspended', 'expired' => 'Expired', 'terminated' => 'Terminated'])->required(), TextInput::make('customer_id')->numeric()]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('starts_on')->date(), TextColumn::make('ends_on')->date()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListSlaContracts::route('/'), 'create' => CreateSlaContract::route('/create')];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()->current_team_id;

        return $data;
    }
}
