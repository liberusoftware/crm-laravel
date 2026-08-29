<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Filament\Resources\QuotaResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\QuotasAndIncentives\Filament\Resources\QuotaResource;

final class ListQuotas extends ListRecords
{
    protected static string $resource = QuotaResource::class;
}
