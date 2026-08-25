<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\RevenueLifecycle\Models\RevenueOrder;
use Liberu\CRM\RevenueLifecycle\Services\RevenuePolicy;

final class CreateOrder
{
    public function __construct(private readonly RevenuePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): RevenueOrder
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['opportunity_id' => ['required', 'integer'], 'value' => ['required', 'numeric', 'min:0'], 'billing_reference' => ['nullable', 'string', 'max:255']])->validate();

        return RevenueOrder::query()->create(['team_id' => $teamId, ...$data, 'status' => 'pending']);
    }
}
