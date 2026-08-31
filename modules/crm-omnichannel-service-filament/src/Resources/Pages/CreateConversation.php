<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelServiceFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\OmnichannelServiceFilament\Resources\ConversationResource;

final class CreateConversation extends CreateRecord
{
    protected static string $resource = ConversationResource::class;
}
