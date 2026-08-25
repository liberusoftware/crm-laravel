<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinarsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\EventsAndWebinars\Actions\CheckInAttendee;
use Liberu\CRM\EventsAndWebinars\Actions\CreateEvent;
use Liberu\CRM\EventsAndWebinars\Actions\RecordEventFollowUp;
use Liberu\CRM\EventsAndWebinars\Actions\RegisterAttendee;
use Liberu\CRM\EventsAndWebinars\Models\CrmEvent;
use Liberu\CRM\EventsAndWebinars\Models\EventRegistration;
use Liberu\CRM\EventsAndWebinars\Queries\EventQuery;

final class EventsController extends Controller
{
    public function __construct(private readonly EventQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->events($teamId)->get());
    }

    public function store(CreateEvent $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function register(CrmEvent $event, RegisterAttendee $action): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($action->execute($teamId, $event, request()->all()), 201);
    }

    public function checkIn(EventRegistration $registration, CheckInAttendee $action): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($action->execute($teamId, $registration));
    }

    public function attendance(CrmEvent $event): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->attendance($teamId, $event->id));
    }

    public function followUp(CrmEvent $event, RecordEventFollowUp $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $event, request()->all()), 201);
    }
}
