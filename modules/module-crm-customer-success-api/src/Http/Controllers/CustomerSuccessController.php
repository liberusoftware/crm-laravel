<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccessApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\CustomerSuccess\Actions\OpenSuccessRisk;
use Liberu\CRM\CustomerSuccess\Actions\PlanRenewal;
use Liberu\CRM\CustomerSuccess\Actions\RecordHealthSignal;
use Liberu\CRM\CustomerSuccess\Actions\UpsertSuccessCustomer;
use Liberu\CRM\CustomerSuccess\Models\SuccessCustomer;
use Liberu\CRM\CustomerSuccess\Queries\CustomerSuccessQuery;

final class CustomerSuccessController extends Controller
{
    public function __construct(private readonly CustomerSuccessQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->customers($teamId)->get());
    }

    public function store(UpsertSuccessCustomer $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function signal(SuccessCustomer $customer, RecordHealthSignal $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $customer, request()->all()), 201);
    }

    public function risk(SuccessCustomer $customer, OpenSuccessRisk $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $customer, request()->all()), 201);
    }

    public function renewal(SuccessCustomer $customer, PlanRenewal $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $customer, request()->all()), 201);
    }
}
