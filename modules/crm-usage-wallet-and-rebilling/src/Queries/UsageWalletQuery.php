<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Queries;

use Liberu\CRM\UsageWalletAndRebilling\Models\UsageCharge;
use Liberu\CRM\UsageWalletAndRebilling\Models\UsageImport;
use Liberu\CRM\UsageWalletAndRebilling\Models\UsageWallet;

final class UsageWalletQuery
{
    public function wallet(int $teamId): ?UsageWallet
    {
        return UsageWallet::query()->where('team_id', $teamId)->first();
    }

    public function imports(int $teamId)
    {
        return UsageImport::query()->where('team_id', $teamId)->latest()->paginate(25);
    }

    public function charges(int $teamId)
    {
        return UsageCharge::query()->where('team_id', $teamId)->latest()->paginate(25);
    }

    public function summary(int $teamId): array
    {
        return ['imports' => UsageImport::query()->where('team_id', $teamId)->count(), 'charges' => UsageCharge::query()->where('team_id', $teamId)->count(), 'wallet' => $this->wallet($teamId)];
    }
}
