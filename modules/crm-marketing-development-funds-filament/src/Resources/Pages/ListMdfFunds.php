<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFundsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\MarketingDevelopmentFundsFilament\Resources\MdfFundResource;

final class ListMdfFunds extends ListRecords
{
    protected static string $resource = MdfFundResource::class;
}
