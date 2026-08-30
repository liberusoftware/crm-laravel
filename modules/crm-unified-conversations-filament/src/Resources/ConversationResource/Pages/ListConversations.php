<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Filament\Resources\ConversationResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\UnifiedConversations\Filament\Resources\ConversationResource;

final class ListConversations extends ListRecords
{
    protected static string $resource = ConversationResource::class;
}
