<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResourcesFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\MarketingResourcesFilament\Resources\MarketingResourceResource;

final class CreateMarketingResource extends CreateRecord
{
    protected static string $resource = MarketingResourceResource::class;
}
