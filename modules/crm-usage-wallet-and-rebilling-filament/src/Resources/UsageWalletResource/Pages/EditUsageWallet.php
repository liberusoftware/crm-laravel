<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Filament\Resources\UsageWalletResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\UsageWalletAndRebilling\Filament\Resources\UsageWalletResource;

final class EditUsageWallet extends EditRecord
{
    protected static string $resource = UsageWalletResource::class;
}
