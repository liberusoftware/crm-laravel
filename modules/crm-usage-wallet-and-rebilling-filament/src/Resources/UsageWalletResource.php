<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\UsageWalletAndRebilling\Filament\Resources\UsageWalletResource\Pages\EditUsageWallet;
use Liberu\CRM\UsageWalletAndRebilling\Filament\Resources\UsageWalletResource\Pages\ListUsageWallets;
use Liberu\CRM\UsageWalletAndRebilling\Models\UsageWallet;

final class UsageWalletResource extends Resource
{
    protected static ?string $model = UsageWallet::class;

    protected static ?string $slug = 'usage-wallets';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('currency')->required()->maxLength(3), TextInput::make('threshold')->numeric()->required(), TextInput::make('reload_amount')->numeric()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('currency'), TextColumn::make('balance'), TextColumn::make('threshold'), TextColumn::make('status')]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()->current_team_id;

        return $data;
    }

    public static function getPages(): array
    {
        return ['index' => ListUsageWallets::route('/'), 'edit' => EditUsageWallet::route('/{record}/edit')];
    }
}
