<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFundsFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\MarketingDevelopmentFundsFilament\Resources\MdfFundResource;

final class EditMdfFund extends EditRecord
{
    protected static string $resource = MdfFundResource::class;
}
