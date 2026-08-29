<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Projects\Models\Project;
use Liberu\CRM\Projects\Services\ProjectPolicy;

final class UpdateProject
{
    public function __construct(private readonly ProjectPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $projectId, array $input): Project
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, [
            'template_id' => ['nullable', 'integer'],
            'customer_id' => ['nullable', 'integer'],
            'opportunity_id' => ['nullable', 'integer'],
            'owner_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ])->validate();
        $project = Project::query()->where('team_id', $teamId)->findOrFail($projectId);
        $project->update($data);

        return $project->refresh();
    }
}
