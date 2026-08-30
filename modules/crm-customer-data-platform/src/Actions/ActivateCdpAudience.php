<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatform\Actions;

use Liberu\CRM\CustomerDataPlatform\Models\CdpAudience;
use Liberu\CRM\CustomerDataPlatform\Services\CdpPolicy;

final class ActivateCdpAudience
{
    public function __construct(private readonly CdpPolicy $policy) {}

    public function execute(int $teamId, int $userId, CdpAudience $audience, string $destination): CdpAudience
    {
        abort_unless($audience->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        abort_unless($destination !== '', 422);
        $audience->update(['status' => 'active', 'activations' => [...(array) $audience->activations, $destination]]);

        return $audience->refresh();
    }
}
