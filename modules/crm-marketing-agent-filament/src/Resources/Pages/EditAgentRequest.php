<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgentFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\MarketingAgentFilament\Resources\AgentRequestResource;

final class EditAgentRequest extends EditRecord
{
    protected static string $resource = AgentRequestResource::class;
}
