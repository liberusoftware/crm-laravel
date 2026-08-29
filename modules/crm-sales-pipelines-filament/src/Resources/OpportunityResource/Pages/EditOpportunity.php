<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament\Resources\OpportunityResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\SalesPipelines\Filament\Resources\OpportunityResource;

final class EditOpportunity extends EditRecord
{
    protected static string $resource = OpportunityResource::class;
}
