<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Actions;

use Liberu\CRM\Projects\Models\Project;
use Liberu\CRM\Projects\Services\ProjectPolicy;

final class HandoffOpportunity
{
    public function __construct(private readonly ProjectPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $projectId, int $opportunityId): Project
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $project = Project::query()->where('team_id', $teamId)->findOrFail($projectId);
        $project->update(['opportunity_id' => $opportunityId]);

        return $project->refresh();
    }
}
