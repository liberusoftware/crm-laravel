<?php

declare(strict_types=1);

namespace Liberu\CRM\EnrichmentFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\EnrichmentFilament\Resources\EnrichmentProfileResource;

final class CreateEnrichmentProfile extends CreateRecord
{
    protected static string $resource = EnrichmentProfileResource::class;
}
