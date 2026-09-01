<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagementApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\BusinessProcessManagement\Actions\AdvanceProcess;
use Liberu\CRM\BusinessProcessManagement\Actions\CreateProcess;
use Liberu\CRM\BusinessProcessManagement\Actions\PublishProcess;
use Liberu\CRM\BusinessProcessManagement\Actions\StartProcess;
use Liberu\CRM\BusinessProcessManagement\Models\Process;
use Liberu\CRM\BusinessProcessManagement\Models\ProcessRun;
use Liberu\CRM\BusinessProcessManagement\Queries\ProcessQuery;

final class BusinessProcessesController extends Controller
{
    public function __construct(private readonly ProcessQuery $query) {}

    private function context(Request $request): array
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        $teamId = (int) $user->getAttribute('current_team_id');
        abort_unless($teamId > 0, 403);

        return [$teamId, (int) $user->getKey()];
    }

    public function index(Request $request): JsonResponse
    {
        [$teamId] = $this->context($request);
        $processes = $this->query->processes($teamId)->paginate(min($request->integer('per_page', 25), 100));

        return response()->json(['data' => $processes->getCollection()->map(fn (Process $process): array => $process->toArray())->values(), 'meta' => ['current_page' => $processes->currentPage(), 'last_page' => $processes->lastPage(), 'per_page' => $processes->perPage(), 'total' => $processes->total()]]);
    }

    public function store(Request $request, CreateProcess $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context($request);

        return response()->json(['data' => $action->execute($teamId, $actorId, $request->all())->toArray()], 201);
    }

    public function publish(Request $request, Process $process, PublishProcess $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context($request);

        return response()->json(['data' => $action->execute($teamId, $actorId, $process)->toArray()]);
    }

    public function start(Request $request, Process $process, StartProcess $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context($request);

        return response()->json(['data' => $action->execute($teamId, $actorId, $process, (array) $request->input('context', []))->toArray()], 201);
    }

    public function advance(Request $request, ProcessRun $run, AdvanceProcess $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context($request);

        return response()->json(['data' => $action->execute($teamId, $actorId, $run, (array) $request->input('context', []))->toArray()]);
    }

    public function runs(Request $request): JsonResponse
    {
        [$teamId] = $this->context($request);
        $runs = $this->query->runs($teamId)->with('events')->paginate(min($request->integer('per_page', 25), 100));

        return response()->json(['data' => $runs->getCollection()->map(fn (ProcessRun $run): array => $run->toArray())->values(), 'meta' => ['current_page' => $runs->currentPage(), 'last_page' => $runs->lastPage(), 'per_page' => $runs->perPage(), 'total' => $runs->total()]]);
    }
}
