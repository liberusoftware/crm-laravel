<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCourses\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\LearningAndCourses\Models\LearningEnrollment;
use Liberu\CRM\LearningAndCourses\Models\LearningRecord;
use Liberu\CRM\LearningAndCourses\Services\LearningPolicy;

final class RecordLearningProgress
{
    public function __construct(private readonly LearningPolicy $policy) {}

    public function execute(int $teamId, int $userId, LearningEnrollment $enrollment, array $input): LearningRecord
    {
        abort_unless($enrollment->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:progress,assessment,certificate,access'], 'status' => ['required', 'in:recorded,passed,failed,issued,revoked'], 'lesson_id' => ['nullable', 'integer'], 'score' => ['nullable', 'numeric', 'between:0,100'], 'certificate_reference' => ['nullable', 'string', 'max:255'], 'payload' => ['nullable', 'array']])->validate();
        $record = LearningRecord::query()->create(['team_id' => $teamId, 'enrollment_id' => $enrollment->id, ...$data]);
        if ($data['kind'] === 'progress') {
            $enrollment->update(['progress' => $data['score'] ?? $enrollment->progress]);
        }if ($data['kind'] === 'certificate' && $data['status'] === 'issued') {
            $enrollment->update(['status' => 'completed', 'completed_at' => now()]);
        }

        return $record;
    }
}
