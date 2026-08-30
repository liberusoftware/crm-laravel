<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspace\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Models\PaymentTransaction;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Services\OrdersPaymentsPolicy;

final class CreateTransaction
{
    public function __construct(private readonly OrdersPaymentsPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PaymentTransaction
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['reference' => ['required', 'string', 'max:255'], 'kind' => ['required', 'in:payment_link,deposit,order,subscription'], 'currency' => ['required', 'string', 'size:3'], 'amount' => ['required', 'numeric', 'min:0.01'], 'external_reference' => ['nullable', 'string', 'max:255'], 'metadata' => ['nullable', 'array']])->validate();

        return PaymentTransaction::query()->create(['team_id' => $teamId, ...$data]);
    }
}
