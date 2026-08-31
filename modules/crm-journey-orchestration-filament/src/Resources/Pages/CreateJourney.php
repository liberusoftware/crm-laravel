<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestrationFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\JourneyOrchestrationFilament\Resources\JourneyResource;

final class CreateJourney extends CreateRecord
{
    protected static string $resource = JourneyResource::class;
}
