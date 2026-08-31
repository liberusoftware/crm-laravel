<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnelsFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\LandingPagesAndFunnelsFilament\Resources\FunnelResource;

final class EditFunnel extends EditRecord
{
    protected static string $resource = FunnelResource::class;
}
