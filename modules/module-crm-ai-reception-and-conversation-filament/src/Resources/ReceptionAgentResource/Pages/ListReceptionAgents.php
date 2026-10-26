<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversationFilament\Resources\ReceptionAgentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\AIReceptionAndConversationFilament\Resources\ReceptionAgentResource;

final class ListReceptionAgents extends ListRecords
{
    protected static string $resource = ReceptionAgentResource::class;
}
