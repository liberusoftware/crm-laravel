<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearchApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\CrmSearch\Actions\IndexSearchDocument;
use Liberu\CRM\CrmSearch\Actions\RecordSearchRecent;
use Liberu\CRM\CrmSearch\Actions\SaveSearchView;
use Liberu\CRM\CrmSearch\Queries\CrmSearchQuery;

final class CrmSearchController extends Controller
{
    public function __construct(private readonly CrmSearchQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function search(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->search($teamId, (string) request('q', ''))->paginate(25));
    }

    public function index(IndexSearchDocument $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function views(): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($this->query->views($teamId, $userId)->get());
    }

    public function saveView(SaveSearchView $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function recents(): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($this->query->recents($teamId, $userId)->limit(25)->get());
    }

    public function recent(RecordSearchRecent $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }
}
