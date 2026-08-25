<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\RevenueLifecycle\Filament\Resources\AssetResource\Pages\ListAssets;
use Liberu\CRM\RevenueLifecycle\Models\RevenueAsset;

final class AssetResource extends Resource
{
    protected static ?string $model = RevenueAsset::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), TextInput::make('customer_id')->numeric()->required(), Select::make('status')->options(['active' => 'Active', 'pending' => 'Pending', 'cancelled' => 'Cancelled', 'expired' => 'Expired'])->required(), Select::make('lifecycle_action')->options(['purchase' => 'Purchase', 'renewal' => 'Renewal', 'upgrade' => 'Upgrade', 'downgrade' => 'Downgrade', 'cancellation' => 'Cancellation']), DatePicker::make('renewal_date')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name'), TextColumn::make('customer_id'), TextColumn::make('status')->badge(), TextColumn::make('lifecycle_action'), TextColumn::make('renewal_date')->date()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListAssets::route('/')];
    }
}
