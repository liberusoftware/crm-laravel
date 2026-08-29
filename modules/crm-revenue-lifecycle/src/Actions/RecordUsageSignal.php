<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\RevenueLifecycle\Models\RevenueAsset;
use Liberu\CRM\RevenueLifecycle\Services\RevenuePolicy;

final class RecordUsageSignal
{
    public function __construct(private readonly RevenuePolicy $policy) {}

    public function execute(int $teamId, int $userId, int $assetId, array $input): RevenueAsset
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['feature' => ['required', 'string', 'max:100'], 'value' => ['required', 'numeric'], 'recorded_at' => ['nullable', 'date']])->validate();
        $asset = RevenueAsset::query()->where('team_id', $teamId)->findOrFail($assetId);
        $signals = $asset->usage_signals ?? [];
        $signals[$data['feature']] = ['value' => $data['value'], 'recorded_at' => $data['recorded_at'] ?? now()->toIso8601String()];
        $asset->update(['usage_signals' => $signals]);

        return $asset->refresh();
    }
}
