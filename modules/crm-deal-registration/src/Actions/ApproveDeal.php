<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistration\Actions;

use Liberu\CRM\DealRegistration\Models\DealRegistration;
use Liberu\CRM\DealRegistration\Models\DealRegistrationEvent;
use Liberu\CRM\DealRegistration\Services\DealRegistrationPolicy;

final class ApproveDeal
{
    public function __construct(private readonly DealRegistrationPolicy $policy) {}

    public function execute(int $teamId, int $userId, DealRegistration $deal, int $protectionDays = 90): DealRegistration
    {
        abort_unless($deal->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        abort_unless($deal->status === 'pending', 422);
        $deal->update(['status' => 'protected', 'protection_until' => now()->addDays($protectionDays)]);
        DealRegistrationEvent::query()->create(['team_id' => $teamId, 'deal_id' => $deal->id, 'actor_id' => $userId, 'event' => 'approved', 'occurred_at' => now()]);

        return $deal->refresh();
    }
}
