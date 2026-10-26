<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistrationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\DealRegistration\Actions\ApproveDeal;
use Liberu\CRM\DealRegistration\Actions\CollaborateOnDeal;
use Liberu\CRM\DealRegistration\Actions\SubmitDeal;
use Liberu\CRM\DealRegistration\Models\DealRegistration;
use Liberu\CRM\DealRegistration\Queries\DealRegistrationQuery;

final class DealRegistrationController extends Controller
{
    public function __construct(private readonly DealRegistrationQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->deals($teamId)->get());
    }

    public function store(SubmitDeal $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function approve(DealRegistration $deal, ApproveDeal $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $deal, (int) request('protection_days', 90)));
    }

    public function collaborate(DealRegistration $deal, CollaborateOnDeal $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $deal, request()->all()));
    }
}
