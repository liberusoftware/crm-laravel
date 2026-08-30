<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SaasPackaging\Events\SubscriptionChanged;
use Liberu\CRM\SaasPackaging\Models\SaasPlan;
use Liberu\CRM\SaasPackaging\Models\SaasSubscription;
use Liberu\CRM\SaasPackaging\Services\SaasPolicy;

final class UpdateSubscription
{
    public function execute(int $teamId, int $actorId, array $data): SaasSubscription
    {
        if (! app(SaasPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        validator($data, [
            'plan_id' => ['required', 'integer'],
            'status' => ['required', 'in:active,suspended,cancelled,trialing'],
            'billing_provider' => ['nullable', 'string', 'max:100'],
            'billing_reference' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $plan = SaasPlan::query()->where('active', true)->findOrFail($data['plan_id']);
        $subscription = SaasSubscription::query()->where('team_id', $teamId)->firstOrFail();
        $subscription->fill([
            'plan_id' => $plan->id,
            'status' => $data['status'],
            'billing_provider' => $data['billing_provider'] ?? null,
            'billing_reference' => $data['billing_reference'] ?? null,
            'cancelled_at' => $data['status'] === 'cancelled' ? now() : null,
        ]);
        $subscription->save();
        SubscriptionChanged::dispatch($subscription, 'updated');

        return $subscription;
    }
}
