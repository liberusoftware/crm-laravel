<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SaasPackaging\Events\SubscriptionChanged;
use Liberu\CRM\SaasPackaging\Models\SaasSubscription;
use Liberu\CRM\SaasPackaging\Services\SaasPolicy;

final class ChangeSubscriptionStatus
{
    public function execute(int $teamId, int $actorId, string $status): SaasSubscription
    {
        if (! app(SaasPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }if (! in_array($status, ['active', 'suspended', 'cancelled'], true)) {
            throw ValidationException::withMessages(['status' => 'Invalid subscription status.']);
        }$sub = SaasSubscription::query()->where('team_id', $teamId)->firstOrFail();
        $sub->status = $status;
        if ($status === 'cancelled') {
            $sub->cancelled_at = now();
        }$sub->save();
        SubscriptionChanged::dispatch($sub, $status);

        return $sub;
    }
}
