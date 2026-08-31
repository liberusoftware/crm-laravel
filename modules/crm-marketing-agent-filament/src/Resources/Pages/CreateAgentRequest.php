<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgentFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\MarketingAgentFilament\Resources\AgentRequestResource;

final class CreateAgentRequest extends CreateRecord
{
    protected static string $resource = AgentRequestResource::class;
}
