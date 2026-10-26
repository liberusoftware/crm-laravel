<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSalesFilament\Resources\ChannelOpportunityResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ChannelSalesFilament\Resources\ChannelOpportunityResource;

final class ListChannelOpportunities extends ListRecords
{
    protected static string $resource = ChannelOpportunityResource::class;
}
