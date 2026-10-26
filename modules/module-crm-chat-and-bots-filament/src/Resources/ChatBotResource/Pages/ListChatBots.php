<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBotsFilament\Resources\ChatBotResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ChatAndBotsFilament\Resources\ChatBotResource;

final class ListChatBots extends ListRecords
{
    protected static string $resource = ChatBotResource::class;
}
