<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessagingFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\MobileMessagingFilament\Resources\MessagingCampaignResource;

final class CreateMessagingCampaign extends CreateRecord
{
    protected static string $resource = MessagingCampaignResource::class;
}
