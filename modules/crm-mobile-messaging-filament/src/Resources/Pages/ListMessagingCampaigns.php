<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessagingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\MobileMessagingFilament\Resources\MessagingCampaignResource;

final class ListMessagingCampaigns extends ListRecords
{
    protected static string $resource = MessagingCampaignResource::class;
}
