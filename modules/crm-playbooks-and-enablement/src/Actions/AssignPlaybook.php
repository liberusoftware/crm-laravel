<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PlaybooksAndEnablement\Models\Playbook;
use Liberu\CRM\PlaybooksAndEnablement\Models\PlaybookAssignment;
use Liberu\CRM\PlaybooksAndEnablement\Services\PlaybookPolicy;

final class AssignPlaybook
{
    public function __construct(private readonly PlaybookPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PlaybookAssignment
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['playbook_id' => ['required', 'integer'], 'assignee_id' => ['required', 'integer'], 'checklist' => ['nullable', 'array']])->validate();
        Playbook::query()->where('team_id', $teamId)->where('active', true)->findOrFail($data['playbook_id']);

        return PlaybookAssignment::query()->firstOrCreate(['team_id' => $teamId, 'playbook_id' => $data['playbook_id'], 'user_id' => $data['assignee_id']], ['team_id' => $teamId, 'checklist' => $data['checklist'] ?? []]);
    }
}
