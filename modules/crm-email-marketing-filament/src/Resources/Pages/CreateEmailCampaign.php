<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketingFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\EmailMarketingFilament\Resources\EmailCampaignResource;

final class CreateEmailCampaign extends CreateRecord
{
    protected static string $resource = EmailCampaignResource::class;
}
