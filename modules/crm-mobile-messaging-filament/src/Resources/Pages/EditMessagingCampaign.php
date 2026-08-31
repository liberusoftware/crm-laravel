<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessagingFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\MobileMessagingFilament\Resources\MessagingCampaignResource;

final class EditMessagingCampaign extends EditRecord
{
    protected static string $resource = MessagingCampaignResource::class;
}
