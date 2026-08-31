<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgentFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\MarketingAgentFilament\Resources\AgentRequestResource;

final class ListAgentRequests extends ListRecords
{
    protected static string $resource = AgentRequestResource::class;
}
