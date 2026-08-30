<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfServiceFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\CustomerSelfService\Models\SelfServiceCase;

final class SelfServiceCaseResource extends Resource
{
    protected static ?string $model = SelfServiceCase::class;

    protected static ?string $navigationLabel = 'Self-service cases';

    public static function getPages(): array
    {
        return [];
    }
}
