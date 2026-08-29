<?php

declare(strict_types=1);

namespace Liberu\CRM\AnalyticsFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\Analytics\Models\AnalyticsAsset;

final class AnalyticsAssetResource extends Resource
{
    protected static ?string $model = AnalyticsAsset::class;

    protected static ?string $navigationLabel = 'Analytics';
}
