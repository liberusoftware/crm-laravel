<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Actions;

use Liberu\CRM\Projects\Events\ProjectStatusChanged;
use Liberu\CRM\Projects\Models\Project;
use Liberu\CRM\Projects\Services\ProjectPolicy;

final class ChangeProjectStatus
{
    public function __construct(private readonly ProjectPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $projectId, string $status): Project
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        abort_unless(in_array($status, ['planning', 'active', 'at_risk', 'on_hold', 'completed', 'cancelled'], true), 422);
        $project = Project::query()->where('team_id', $teamId)->findOrFail($projectId);
        $project->update(['status' => $status]);
        event(new ProjectStatusChanged($project, $status));

        return $project->refresh();
    }
}
