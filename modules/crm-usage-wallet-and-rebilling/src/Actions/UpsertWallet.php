<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\UsageWalletAndRebilling\Models\UsageWallet;
use Liberu\CRM\UsageWalletAndRebilling\Services\UsageAudit;
use Liberu\CRM\UsageWalletAndRebilling\Services\UsagePolicy;

final class UpsertWallet
{
    public function execute(int $teamId, int $actorId, array $data): UsageWallet
    {
        if (! app(UsagePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }
        $data = validator($data, ['currency' => ['required', 'regex:/^[A-Z]{3}$/'], 'threshold' => ['required', 'numeric', 'min:0'], 'reload_amount' => ['required', 'numeric', 'min:0']])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $data) {
            $wallet = UsageWallet::query()->lockForUpdate()->firstOrNew(['team_id' => $teamId]);
            $wallet->fill($data);
            $wallet->save();
            app(UsageAudit::class)->record($teamId, $actorId, 'wallet_updated', ['wallet_id' => $wallet->id]);

            return $wallet->fresh();
        });
    }
}
