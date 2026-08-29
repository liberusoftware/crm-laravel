<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PlaybooksAndEnablement\Models\PlaybookUsage;
use Liberu\CRM\PlaybooksAndEnablement\Services\PlaybookPolicy;

final class RecordPlaybookUsage
{
    public function __construct(private readonly PlaybookPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PlaybookUsage
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['playbook_id' => ['required', 'integer'], 'event' => ['required', 'in:viewed,started,step_completed,completed'], 'payload' => ['nullable', 'array']])->validate();

        return PlaybookUsage::query()->create(['team_id' => $teamId, 'user_id' => $userId, ...$data, 'occurred_at' => now()]);
    }
}
