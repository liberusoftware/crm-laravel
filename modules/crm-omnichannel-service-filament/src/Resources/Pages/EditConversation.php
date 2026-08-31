<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelServiceFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\OmnichannelServiceFilament\Resources\ConversationResource;

final class EditConversation extends EditRecord
{
    protected static string $resource = ConversationResource::class;
}
