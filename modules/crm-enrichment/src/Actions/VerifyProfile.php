<?php

declare(strict_types=1);

namespace Liberu\CRM\Enrichment\Actions;

use Liberu\CRM\Enrichment\Models\EnrichmentProfile;
use Liberu\CRM\Enrichment\Services\EnrichmentPolicy;

final class VerifyProfile
{
    public function __construct(private readonly EnrichmentPolicy $policy) {}

    public function execute(int $teamId, int $userId, EnrichmentProfile $profile, bool $verified): EnrichmentProfile
    {
        abort_unless($profile->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $profile->update(['verification' => [...(array) $profile->verification, 'verified' => $verified, 'verified_at' => now()->toIso8601String()]]);

        return $profile->refresh();
    }
}
