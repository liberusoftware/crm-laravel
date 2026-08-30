<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatformFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\CustomerDataPlatform\Models\CdpProfile;

final class CdpProfileResource extends Resource
{
    protected static ?string $model = CdpProfile::class;

    protected static ?string $navigationLabel = 'Unified profiles';

    public static function getPages(): array
    {
        return [];
    }
}
