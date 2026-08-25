<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PlaybooksAndEnablement\Models\Playbook;
use Liberu\CRM\PlaybooksAndEnablement\Models\PlaybookRecommendation;
use Liberu\CRM\PlaybooksAndEnablement\Services\PlaybookPolicy;

final class RecommendPlaybook
{
    public function __construct(private readonly PlaybookPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PlaybookRecommendation
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['playbook_id' => ['required', 'integer'], 'subject_type' => ['required', 'string', 'max:255'], 'subject_id' => ['required', 'integer'], 'reason' => ['required', 'string', 'max:1000']])->validate();
        Playbook::query()->where('team_id', $teamId)->findOrFail($data['playbook_id']);

        return PlaybookRecommendation::query()->create(['team_id' => $teamId, ...$data]);
    }
}
