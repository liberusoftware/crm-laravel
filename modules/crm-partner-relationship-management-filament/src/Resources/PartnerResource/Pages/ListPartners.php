<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Filament\Resources\PartnerResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\PartnerRelationshipManagement\Filament\Resources\PartnerResource;

final class ListPartners extends ListRecords
{
    protected static string $resource = PartnerResource::class;
}
