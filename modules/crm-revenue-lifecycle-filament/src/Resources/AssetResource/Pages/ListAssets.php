<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Filament\Resources\AssetResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\RevenueLifecycle\Filament\Resources\AssetResource;

final class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;
}
