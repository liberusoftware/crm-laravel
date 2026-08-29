<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SaasPackaging\Events\SubscriptionChanged;
use Liberu\CRM\SaasPackaging\Models\SaasPlan;
use Liberu\CRM\SaasPackaging\Models\SaasSubscription;
use Liberu\CRM\SaasPackaging\Services\SaasPolicy;

final class ProvisionSubscription
{
    public function execute(int $teamId, int $actorId, array $data): SaasSubscription
    {
        if (! app(SaasPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['plan_id' => ['required', 'integer'], 'billing_provider' => ['nullable', 'string', 'max:100'], 'billing_reference' => ['nullable', 'string', 'max:255']])->validate();
        $plan = SaasPlan::query()->where('active', true)->findOrFail($data['plan_id']);
        $sub = SaasSubscription::query()->updateOrCreate(['team_id' => $teamId], ['plan_id' => $plan->id, 'status' => $plan->trial_days > 0 ? 'trialing' : 'active', 'trial_ends_at' => $plan->trial_days > 0 ? now()->addDays($plan->trial_days) : null, 'billing_provider' => $data['billing_provider'] ?? null, 'billing_reference' => $data['billing_reference'] ?? null]);
        SubscriptionChanged::dispatch($sub, 'provisioned');

        return $sub;
    }
}
