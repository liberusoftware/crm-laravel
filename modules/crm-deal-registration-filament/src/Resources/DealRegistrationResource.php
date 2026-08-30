<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistrationFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\DealRegistration\Models\DealRegistration;

final class DealRegistrationResource extends Resource
{
    protected static ?string $model = DealRegistration::class;

    protected static ?string $navigationLabel = 'Deal registration';

    public static function getPages(): array
    {
        return [];
    }
}
