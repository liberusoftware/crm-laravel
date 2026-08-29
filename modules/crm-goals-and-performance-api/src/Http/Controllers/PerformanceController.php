<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformanceApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\GoalsAndPerformance\Actions\CreateGoal;
use Liberu\CRM\GoalsAndPerformance\Actions\RecordReview;
use Liberu\CRM\GoalsAndPerformance\Actions\UpdateGoalActual;
use Liberu\CRM\GoalsAndPerformance\Models\PerformanceGoal;
use Liberu\CRM\GoalsAndPerformance\Queries\PerformanceQuery;

final class PerformanceController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, PerformanceQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function store(Request $r, CreateGoal $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function event(Request $r, PerformanceGoal $goal, UpdateGoalActual $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $goal, $r->all()), 201);
    }

    public function review(Request $r, RecordReview $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }
}
