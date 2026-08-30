<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFundsFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\MarketingDevelopmentFunds\Models\MdfFund;

final class MdfFundResource extends Resource
{
    protected static ?string $model = MdfFund::class;

    protected static ?string $navigationLabel = 'Marketing Funds';

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
