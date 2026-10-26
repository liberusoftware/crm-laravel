<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordinationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\FieldServiceCoordination\Actions\CreateWorkType;
use Liberu\CRM\FieldServiceCoordination\Actions\HandOffMaintenance;
use Liberu\CRM\FieldServiceCoordination\Actions\RecordServiceHistory;
use Liberu\CRM\FieldServiceCoordination\Actions\ScheduleAppointment;
use Liberu\CRM\FieldServiceCoordination\Models\ServiceAppointment;
use Liberu\CRM\FieldServiceCoordination\Queries\FieldServiceQuery;

final class FieldServiceController extends Controller
{
    public function __construct(private readonly FieldServiceQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function workTypes(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->workTypes($teamId)->get());
    }

    public function appointments(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->appointments($teamId)->get());
    }

    public function storeWorkType(CreateWorkType $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function schedule(ScheduleAppointment $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function history(ServiceAppointment $appointment, RecordServiceHistory $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $appointment, request()->all()), 201);
    }

    public function handoff(ServiceAppointment $appointment, HandOffMaintenance $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $appointment, request()->all()), 201);
    }
}
