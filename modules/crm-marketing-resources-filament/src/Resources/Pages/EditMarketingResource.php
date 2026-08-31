<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResourcesFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\MarketingResourcesFilament\Resources\MarketingResourceResource;

final class EditMarketingResource extends EditRecord
{
    protected static string $resource = MarketingResourceResource::class;
}
