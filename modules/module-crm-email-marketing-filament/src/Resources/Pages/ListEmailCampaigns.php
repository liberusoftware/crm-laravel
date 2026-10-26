<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\EmailMarketingFilament\Resources\EmailCampaignResource;

final class ListEmailCampaigns extends ListRecords
{
    protected static string $resource = EmailCampaignResource::class;
}
