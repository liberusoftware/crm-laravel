<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccess\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\CustomerSuccess\Models\SuccessCustomer;
use Liberu\CRM\CustomerSuccess\Services\CustomerSuccessPolicy;

final class UpsertSuccessCustomer
{
    public function __construct(private readonly CustomerSuccessPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): SuccessCustomer
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['customer_key' => ['required', 'string', 'max:160'], 'segment' => ['nullable', 'string', 'max:80'], 'lifecycle' => ['nullable', 'in:onboarding,adopted,expansion,at_risk,churned'], 'health_score' => ['nullable', 'integer', 'between:0,100'], 'onboarding' => ['nullable', 'array'], 'success_plan' => ['nullable', 'array'], 'objectives' => ['nullable', 'array']])->validate();

        return SuccessCustomer::query()->updateOrCreate(['team_id' => $teamId, 'customer_key' => $data['customer_key']], $data + ['team_id' => $teamId]);
    }
}
