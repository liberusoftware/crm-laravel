<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccessFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\CustomerSuccess\Models\SuccessCustomer;

final class SuccessCustomerResource extends Resource
{
    protected static ?string $model = SuccessCustomer::class;

    protected static ?string $navigationLabel = 'Customer success';

    public static function getPages(): array
    {
        return [];
    }
}
