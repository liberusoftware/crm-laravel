<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformance\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\GoalsAndPerformance\Models\PerformanceReview;
use Liberu\CRM\GoalsAndPerformance\Services\PerformancePolicy;

final class RecordReview
{
    public function __construct(private readonly PerformancePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PerformanceReview
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['subject_id' => ['required', 'integer'], 'period' => ['required', 'string', 'max:50'], 'reviewer_id' => ['required', 'integer'], 'status' => ['required', 'in:draft,submitted,completed'], 'score' => ['nullable', 'integer', 'between:0,100'], 'summary' => ['nullable', 'string'], 'coaching_plan' => ['nullable', 'array']])->validate();

        return PerformanceReview::query()->updateOrCreate(['team_id' => $teamId, 'subject_id' => $data['subject_id'], 'period' => $data['period']], $data);
    }
}
