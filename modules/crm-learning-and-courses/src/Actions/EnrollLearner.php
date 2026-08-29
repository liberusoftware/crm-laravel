<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCourses\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\LearningAndCourses\Models\LearningCourse;
use Liberu\CRM\LearningAndCourses\Models\LearningEnrollment;
use Liberu\CRM\LearningAndCourses\Services\LearningPolicy;

final class EnrollLearner
{
    public function __construct(private readonly LearningPolicy $policy) {}

    public function execute(int $teamId, int $userId, LearningCourse $course, array $input): LearningEnrollment
    {
        abort_unless($course->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['member_id' => ['required', 'integer'], 'status' => ['nullable', 'in:active,suspended,completed'], 'entitlement_reference' => ['nullable', 'string', 'max:255']])->validate();

        return LearningEnrollment::query()->updateOrCreate(['team_id' => $teamId, 'course_id' => $course->id, 'member_id' => $data['member_id']], ['status' => $data['status'] ?? 'active', 'entitlement_reference' => $data['entitlement_reference'] ?? null]);
    }
}
