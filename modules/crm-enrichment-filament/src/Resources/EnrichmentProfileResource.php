<?php

declare(strict_types=1);

namespace Liberu\CRM\EnrichmentFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\Enrichment\Models\EnrichmentProfile;

final class EnrichmentProfileResource extends Resource
{
    protected static ?string $model = EnrichmentProfile::class;

    protected static ?string $navigationLabel = 'Enrichment';

    public static function getPages(): array
    {
        return [];
    }
}
