<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Filament\Resources\AgentRunResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ProspectingAgent\Filament\Resources\AgentRunResource;

final class ListAgentRuns extends ListRecords
{
    protected static string $resource = AgentRunResource::class;
}
