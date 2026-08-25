<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPack\Actions;

use Liberu\CRM\AutomationPack\Models\AutomationEnrollment;
use Liberu\CRM\AutomationPack\Models\AutomationRecipe;
use Liberu\CRM\AutomationPack\Services\AutomationPackPolicy;

final class EnrollSubject
{
    public function __construct(private readonly AutomationPackPolicy $policy) {}

    /** @param array{subject_key?: string} $input */
    public function execute(int $teamId, int $userId, AutomationRecipe $recipe, array $input): AutomationEnrollment
    {
        abort_unless($recipe->team_id === $teamId && $recipe->status === 'active' && $this->policy->canManage($teamId, $userId), 403);
        $subjectKey = (string) ($input['subject_key'] ?? '');
        abort_unless($subjectKey !== '', 422);

        return AutomationEnrollment::query()->create([
            'team_id' => $teamId,
            'recipe_id' => $recipe->id,
            'subject_key' => $subjectKey,
            'status' => 'enrolled',
            'history' => [['event' => 'enrolled', 'at' => now()->toIso8601String()]],
            'enrolled_at' => now(),
        ]);
    }
}
