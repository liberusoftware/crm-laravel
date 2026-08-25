<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspaceApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\AgencyWorkspace\Actions\CreateAgencyAccount;
use Liberu\CRM\AgencyWorkspace\Actions\GrantAgencyAccess;
use Liberu\CRM\AgencyWorkspace\Actions\UpdateAgencyUsage;
use Liberu\CRM\AgencyWorkspace\Models\AgencyAccount;
use Liberu\CRM\AgencyWorkspace\Queries\AgencyQuery;

final class AgencyWorkspaceController extends Controller
{
    public function __construct(private readonly AgencyQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        return response()->json($this->query->accounts($this->context()[0])->get());
    }

    public function store(CreateAgencyAccount $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context();

        return response()->json($action->execute($teamId, $actorId, request()->all()), 201);
    }

    public function grantAccess(AgencyAccount $account, GrantAgencyAccess $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context();

        return response()->json($action->execute($teamId, $actorId, $account, request()->all()), 201);
    }

    public function usage(AgencyAccount $account, UpdateAgencyUsage $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context();

        return response()->json($action->execute($teamId, $actorId, $account, request()->all()));
    }
}
