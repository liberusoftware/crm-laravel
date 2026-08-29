<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboarding\Actions;

use Liberu\CRM\ClientOnboarding\Models\ClientOnboarding;
use Liberu\CRM\ClientOnboarding\Models\ClientOnboardingStep;

final class CompleteOnboardingStep
{
    public function execute(int $teamId, int $actorId, ClientOnboarding $onboarding, string $kind, string $label, array $evidence = []): ClientOnboardingStep
    {
        abort_unless((int) $onboarding->team_id === $teamId && in_array($kind, ['connection', 'import', 'snapshot', 'verification', 'launch', 'training', 'handoff'], true) && $label !== '' && $evidence !== [], 422);
        $step = ClientOnboardingStep::query()->updateOrCreate(['onboarding_id' => $onboarding->id, 'kind' => $kind, 'label' => $label], ['team_id' => $teamId, 'status' => 'completed', 'actor_id' => $actorId, 'evidence' => $evidence, 'completed_at' => now()]);
        $onboarding->update(['status' => $kind === 'handoff' ? 'complete' : 'in_progress', 'health' => min(100, (int) $onboarding->health + 10)]);

        return $step;
    }
}
