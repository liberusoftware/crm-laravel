<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelServiceFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\OmnichannelServiceFilament\Resources\ConversationResource;

final class ListConversations extends ListRecords
{
    protected static string $resource = ConversationResource::class;
}
