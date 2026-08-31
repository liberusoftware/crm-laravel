<?php

declare(strict_types=1);

namespace Liberu\CRM\EnrichmentFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\EnrichmentFilament\Resources\EnrichmentProfileResource;

final class EditEnrichmentProfile extends EditRecord
{
    protected static string $resource = EnrichmentProfileResource::class;
}
