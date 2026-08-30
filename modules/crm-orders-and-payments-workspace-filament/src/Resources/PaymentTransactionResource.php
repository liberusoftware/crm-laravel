<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspaceFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Models\PaymentTransaction;

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

    public static function getPages(): array
    {
        return [];
    }
}
