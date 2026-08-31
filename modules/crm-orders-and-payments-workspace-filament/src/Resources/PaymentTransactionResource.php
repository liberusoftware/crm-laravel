<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspaceFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Models\PaymentTransaction;
use Liberu\CRM\OrdersAndPaymentsWorkspaceFilament\Resources\Pages\CreatePaymentTransaction;
use Liberu\CRM\OrdersAndPaymentsWorkspaceFilament\Resources\Pages\EditPaymentTransaction;
use Liberu\CRM\OrdersAndPaymentsWorkspaceFilament\Resources\Pages\ListPaymentTransactions;

final class PaymentTransactionResource extends Resource
{
    protected static ?string $model = PaymentTransaction::class;

    protected static ?string $navigationLabel = 'Orders & Payments';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListPaymentTransactions::route('/'), 'create' => CreatePaymentTransaction::route('/create'), 'edit' => EditPaymentTransaction::route('/{record}/edit')];
    }
}
