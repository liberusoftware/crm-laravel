<?php

declare(strict_types=1);

namespace Liberu\CRM\CampaignsFilament\Resources\CampaignResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CampaignsFilament\Resources\CampaignResource;

final class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;
}
