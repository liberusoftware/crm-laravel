<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagementApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\CaseManagement\Actions\OpenCase;
use Liberu\CRM\CaseManagement\Actions\TransitionCase;
use Liberu\CRM\CaseManagement\Models\CaseRecord;
use Liberu\CRM\CaseManagement\Queries\CaseQuery;

final class CaseManagementController extends Controller
{
    public function __construct(private readonly CaseQuery $query) {}

    private function context(Request $request): array
    {
        $u = $request->user();
        abort_unless($u !== null, 401);
        $teamId = (int) $u->getAttribute('current_team_id');
        abort_unless($teamId > 0, 403);

        return [$teamId, (int) $u->getKey()];
    }

    public function index(Request $request): JsonResponse
    {
        [$t] = $this->context($request);
        $cases = $this->query->cases($t)->paginate(min($request->integer('per_page', 25), 100));

        return response()->json(['data' => $cases->getCollection()->map(fn (CaseRecord $case): array => $case->toArray())->values(), 'meta' => ['current_page' => $cases->currentPage(), 'last_page' => $cases->lastPage(), 'per_page' => $cases->perPage(), 'total' => $cases->total()]]);
    }

    public function store(Request $request, OpenCase $a): JsonResponse
    {
        [$t,$u] = $this->context($request);

        return response()->json(['data' => $a->execute($t, $u, $request->all())->toArray()], 201);
    }

    public function transition(Request $request, CaseRecord $case, TransitionCase $a): JsonResponse
    {
        [$t,$u] = $this->context($request);

        return response()->json(['data' => $a->execute($t, $u, $case, (string) $request->input('status'), $request->integer('owner_id') ?: null)->toArray()]);
    }

    public function queue(Request $request): JsonResponse
    {
        [$t] = $this->context($request);
        $cases = $this->query->queue($t, (string) $request->input('status', 'open'))->paginate(min($request->integer('per_page', 25), 100));

        return response()->json(['data' => $cases->getCollection()->map(fn (CaseRecord $case): array => $case->toArray())->values(), 'meta' => ['current_page' => $cases->currentPage(), 'last_page' => $cases->lastPage(), 'per_page' => $cases->perPage(), 'total' => $cases->total()]]);
    }
}
