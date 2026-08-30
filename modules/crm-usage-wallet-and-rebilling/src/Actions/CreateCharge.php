<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\UsageWalletAndRebilling\Models\UsageCharge;
use Liberu\CRM\UsageWalletAndRebilling\Models\UsageImport;
use Liberu\CRM\UsageWalletAndRebilling\Models\UsageWallet;
use Liberu\CRM\UsageWalletAndRebilling\Services\UsageAudit;
use Liberu\CRM\UsageWalletAndRebilling\Services\UsagePolicy;

final class CreateCharge
{
    public function execute(int $teamId, int $actorId, array $data): UsageCharge
    {
        if (! app(UsagePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }
        $data = validator($data, ['usage_import_id' => ['required', 'integer'], 'markup_percent' => ['required', 'numeric', 'min:0', 'max:1000'], 'client_reference' => ['nullable', 'string', 'max:255']])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $data) {
            $import = UsageImport::query()->where('team_id', $teamId)->findOrFail($data['usage_import_id']);
            $wallet = UsageWallet::query()->where('team_id', $teamId)->lockForUpdate()->firstOrFail();
            $cost = (float) $import->getAttribute('amount');
            $charge = round($cost * (1 + (float) $data['markup_percent'] / 100), 6);
            $row = UsageCharge::query()->firstOrCreate(['team_id' => $teamId, 'usage_import_id' => $import->id], ['wallet_id' => $wallet->getKey(), 'client_reference' => $data['client_reference'] ?? null, 'cost' => $cost, 'charge' => $charge, 'currency' => $import->getAttribute('currency'), 'status' => 'posted']);
            $wallet->setAttribute('balance', (float) $wallet->getAttribute('balance') - ($row->wasRecentlyCreated ? $charge : 0));
            $wallet->setAttribute('version', (int) $wallet->getAttribute('version') + ($row->wasRecentlyCreated ? 1 : 0));
            $wallet->save();
            if ($row->wasRecentlyCreated) {
                app(UsageAudit::class)->record($teamId, $actorId, 'client_charge_created', ['charge_id' => $row->id]);
            }

            return $row;
        });
    }
}
