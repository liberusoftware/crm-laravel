<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Filament\Resources\AgentCaseResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ServiceAgent\Filament\Resources\AgentCaseResource;

final class ListAgentCases extends ListRecords
{
    protected static string $resource = AgentCaseResource::class;
}
