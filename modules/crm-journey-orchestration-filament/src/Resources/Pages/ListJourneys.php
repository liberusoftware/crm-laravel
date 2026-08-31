<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestrationFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\JourneyOrchestrationFilament\Resources\JourneyResource;

final class ListJourneys extends ListRecords
{
    protected static string $resource = JourneyResource::class;
}
