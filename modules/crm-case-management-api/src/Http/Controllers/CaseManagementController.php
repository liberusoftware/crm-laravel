<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagementApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\CaseManagement\Actions\OpenCase;
use Liberu\CRM\CaseManagement\Actions\TransitionCase;
use Liberu\CRM\CaseManagement\Models\CaseRecord;
use Liberu\CRM\CaseManagement\Queries\CaseQuery;

final class CaseManagementController extends Controller
{
    public function __construct(private readonly CaseQuery $query) {}

    private function context(): array
    {
        $u = request()->user();

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function index(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->cases($t)->get());
    }

    public function store(OpenCase $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, request()->all()), 201);
    }

    public function transition(CaseRecord $case, TransitionCase $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, $case, (string) request('status'), request('owner_id')));
    }

    public function queue(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->queue($t, (string) request('status', 'open'))->get());
    }
}
