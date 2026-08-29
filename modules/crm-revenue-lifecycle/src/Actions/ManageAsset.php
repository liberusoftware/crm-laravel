<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\RevenueLifecycle\Events\RevenueLifecycleChanged;
use Liberu\CRM\RevenueLifecycle\Models\RevenueAsset;
use Liberu\CRM\RevenueLifecycle\Services\RevenuePolicy;

final class ManageAsset
{
    public function __construct(private readonly RevenuePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): RevenueAsset
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['id' => ['nullable', 'integer'], 'customer_id' => ['required', 'integer'], 'name' => ['required', 'string', 'max:255'], 'status' => ['required', 'in:active,pending,cancelled,expired'], 'lifecycle_action' => ['nullable', 'in:purchase,renewal,upgrade,downgrade,cancellation'], 'renewal_date' => ['nullable', 'date'], 'entitlements' => ['nullable', 'array'], 'usage_signals' => ['nullable', 'array']])->validate();
        $id = $data['id'] ?? null;
        unset($data['id']);
        $asset = RevenueAsset::query()->updateOrCreate(['id' => $id, 'team_id' => $teamId], ['team_id' => $teamId, ...$data]);
        event(new RevenueLifecycleChanged($asset, (string) ($asset->lifecycle_action ?? 'purchase')));

        return $asset;
    }
}
