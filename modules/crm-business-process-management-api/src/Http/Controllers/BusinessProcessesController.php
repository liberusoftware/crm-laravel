<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagementApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
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

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->processes($teamId)->get());
    }

    public function store(CreateProcess $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context();

        return response()->json($action->execute($teamId, $actorId, request()->all()), 201);
    }

    public function publish(Process $process, PublishProcess $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context();

        return response()->json($action->execute($teamId, $actorId, $process));
    }

    public function start(Process $process, StartProcess $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context();

        return response()->json($action->execute($teamId, $actorId, $process, (array) request('context', [])), 201);
    }

    public function advance(ProcessRun $run, AdvanceProcess $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context();

        return response()->json($action->execute($teamId, $actorId, $run, (array) request('context', [])));
    }

    public function runs(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->runs($teamId)->with('events')->get());
    }
}
