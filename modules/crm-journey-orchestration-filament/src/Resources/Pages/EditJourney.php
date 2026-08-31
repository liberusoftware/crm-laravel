<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestrationFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\JourneyOrchestrationFilament\Resources\JourneyResource;

final class EditJourney extends EditRecord
{
    protected static string $resource = JourneyResource::class;
}
