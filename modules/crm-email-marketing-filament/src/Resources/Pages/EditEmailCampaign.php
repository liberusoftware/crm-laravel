<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketingFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\EmailMarketingFilament\Resources\EmailCampaignResource;

final class EditEmailCampaign extends EditRecord
{
    protected static string $resource = EmailCampaignResource::class;
}
