<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccess\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\CustomerSuccess\Models\SuccessCustomer;
use Liberu\CRM\CustomerSuccess\Models\SuccessRenewal;
use Liberu\CRM\CustomerSuccess\Services\CustomerSuccessPolicy;

final class PlanRenewal
{
    public function __construct(private readonly CustomerSuccessPolicy $policy) {}

    public function execute(int $teamId, int $userId, SuccessCustomer $customer, array $input): SuccessRenewal
    {
        abort_unless($customer->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['renewal_date' => ['required', 'date'], 'status' => ['nullable', 'in:upcoming,in_progress,won,lost'], 'value' => ['nullable', 'numeric'], 'attribution' => ['nullable', 'array']])->validate();

        return SuccessRenewal::query()->create(['team_id' => $teamId, 'customer_id' => $customer->id, ...$data]);
    }
}
