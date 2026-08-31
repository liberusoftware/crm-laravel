<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnelsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\LandingPagesAndFunnelsFilament\Resources\FunnelResource;

final class ListFunnels extends ListRecords
{
    protected static string $resource = FunnelResource::class;
}
