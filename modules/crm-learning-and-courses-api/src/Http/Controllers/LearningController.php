<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCoursesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\LearningAndCourses\Actions\CreateCourse;
use Liberu\CRM\LearningAndCourses\Actions\EnrollLearner;
use Liberu\CRM\LearningAndCourses\Actions\RecordLearningProgress;
use Liberu\CRM\LearningAndCourses\Models\LearningCourse;
use Liberu\CRM\LearningAndCourses\Models\LearningEnrollment;
use Liberu\CRM\LearningAndCourses\Queries\LearningQuery;

final class LearningController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, LearningQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function store(Request $r, CreateCourse $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function enroll(Request $r, LearningCourse $course, EnrollLearner $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $course, $r->all()), 201);
    }

    public function record(Request $r, LearningEnrollment $enrollment, RecordLearningProgress $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $enrollment, $r->all()), 201);
    }

    public function records(Request $r, LearningEnrollment $enrollment, LearningQuery $q): JsonResponse
    {
        abort_unless($enrollment->team_id === (int) $r->user()->current_team_id, 404);

        return response()->json($q->records($enrollment->team_id, $enrollment->id)->paginate());
    }
}
