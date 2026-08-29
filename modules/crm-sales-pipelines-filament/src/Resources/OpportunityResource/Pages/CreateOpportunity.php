<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament\Resources\OpportunityResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\SalesPipelines\Filament\Resources\OpportunityResource;

final class CreateOpportunity extends CreateRecord
{
    protected static string $resource = OpportunityResource::class;
}
