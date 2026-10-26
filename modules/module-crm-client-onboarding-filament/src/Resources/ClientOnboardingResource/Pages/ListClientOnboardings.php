<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboardingFilament\Resources\ClientOnboardingResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ClientOnboardingFilament\Resources\ClientOnboardingResource;

final class ListClientOnboardings extends ListRecords
{
    protected static string $resource = ClientOnboardingResource::class;
}
