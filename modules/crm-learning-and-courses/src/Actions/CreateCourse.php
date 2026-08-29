<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCourses\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\LearningAndCourses\Models\LearningCourse;
use Liberu\CRM\LearningAndCourses\Services\LearningPolicy;

final class CreateCourse
{
    public function __construct(private readonly LearningPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): LearningCourse
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['slug' => ['required', 'string', 'max:255'], 'name' => ['required', 'string', 'max:255'], 'status' => ['nullable', 'in:draft,published,archived'], 'metadata' => ['nullable', 'array']])->validate();

        return LearningCourse::query()->create(['team_id' => $teamId, ...$data]);
    }
}
