<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament\Resources\OpportunityResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\SalesPipelines\Filament\Resources\OpportunityResource;

final class ListOpportunities extends ListRecords
{
    protected static string $resource = OpportunityResource::class;
}
