<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnelsFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\LandingPagesAndFunnelsFilament\Resources\FunnelResource;

final class CreateFunnel extends CreateRecord
{
    protected static string $resource = FunnelResource::class;
}
