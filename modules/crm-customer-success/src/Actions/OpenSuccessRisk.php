<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccess\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\CustomerSuccess\Models\SuccessCustomer;
use Liberu\CRM\CustomerSuccess\Models\SuccessRisk;
use Liberu\CRM\CustomerSuccess\Services\CustomerSuccessPolicy;

final class OpenSuccessRisk
{
    public function __construct(private readonly CustomerSuccessPolicy $policy) {}

    public function execute(int $teamId, int $userId, SuccessCustomer $customer, array $input): SuccessRisk
    {
        abort_unless($customer->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'string', 'max:80'], 'severity' => ['required', 'in:low,medium,high,critical'], 'mitigation' => ['nullable', 'string']])->validate();

        return SuccessRisk::query()->create(['team_id' => $teamId, 'customer_id' => $customer->id, 'status' => 'open', ...$data]);
    }
}
