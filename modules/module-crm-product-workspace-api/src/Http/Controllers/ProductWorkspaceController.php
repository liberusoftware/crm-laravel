<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspaceApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\ProductWorkspace\Actions\GrantEntitlement;
use Liberu\CRM\ProductWorkspace\Actions\RecordProductSync;
use Liberu\CRM\ProductWorkspace\Actions\UpsertWorkspaceProduct;
use Liberu\CRM\ProductWorkspace\Queries\ProductWorkspaceQuery;

final class ProductWorkspaceController extends Controller
{
    public function __construct(private readonly ProductWorkspaceQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function products(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->products($teamId)->get());
    }

    public function store(UpsertWorkspaceProduct $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function entitlement(GrantEntitlement $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function sync(RecordProductSync $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }
}
