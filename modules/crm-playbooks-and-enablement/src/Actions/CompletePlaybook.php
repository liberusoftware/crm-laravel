<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PlaybooksAndEnablement\Events\PlaybookCompleted;
use Liberu\CRM\PlaybooksAndEnablement\Models\PlaybookAssignment;
use Liberu\CRM\PlaybooksAndEnablement\Services\PlaybookPolicy;

final class CompletePlaybook
{
    public function __construct(private readonly PlaybookPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $assignmentId, array $input): PlaybookAssignment
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['evidence' => ['required', 'array']])->validate();
        $assignment = PlaybookAssignment::query()->where('team_id', $teamId)->whereIn('status', ['assigned', 'in_progress'])->findOrFail($assignmentId);
        $assignment->update(['status' => 'completed', 'evidence' => $data['evidence'], 'completed_at' => now()]);
        event(new PlaybookCompleted($assignment));

        return $assignment->refresh();
    }
}
