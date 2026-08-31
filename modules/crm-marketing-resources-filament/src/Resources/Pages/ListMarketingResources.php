<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResourcesFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\MarketingResourcesFilament\Resources\MarketingResourceResource;

final class ListMarketingResources extends ListRecords
{
    protected static string $resource = MarketingResourceResource::class;
}
