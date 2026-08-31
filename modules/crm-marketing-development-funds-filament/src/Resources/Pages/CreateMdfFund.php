<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFundsFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\MarketingDevelopmentFundsFilament\Resources\MdfFundResource;

final class CreateMdfFund extends CreateRecord
{
    protected static string $resource = MdfFundResource::class;
}
