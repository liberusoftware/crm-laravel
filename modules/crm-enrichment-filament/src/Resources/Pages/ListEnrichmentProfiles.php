<?php

declare(strict_types=1);

namespace Liberu\CRM\EnrichmentFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\EnrichmentFilament\Resources\EnrichmentProfileResource;

final class ListEnrichmentProfiles extends ListRecords
{
    protected static string $resource = EnrichmentProfileResource::class;
}
