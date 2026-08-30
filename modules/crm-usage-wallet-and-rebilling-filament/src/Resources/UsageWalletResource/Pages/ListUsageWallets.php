<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Filament\Resources\UsageWalletResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\UsageWalletAndRebilling\Filament\Resources\UsageWalletResource;

final class ListUsageWallets extends ListRecords
{
    protected static string $resource = UsageWalletResource::class;
}
