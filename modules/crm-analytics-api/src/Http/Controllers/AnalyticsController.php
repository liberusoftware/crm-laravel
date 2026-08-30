<?php

declare(strict_types=1);

namespace Liberu\CRM\AnalyticsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\Analytics\Actions\CreateAnalyticsAsset;
use Liberu\CRM\Analytics\Actions\ExecuteAnalyticsAsset;
use Liberu\CRM\Analytics\Models\AnalyticsAsset;
use Liberu\CRM\Analytics\Queries\AnalyticsQuery;

final class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        [$team] = $this->context();

        return response()->json($this->query->assets($team)->get());
    }

    public function store(CreateAnalyticsAsset $action): JsonResponse
    {
        [$team,$user] = $this->context();

        return response()->json($action->execute($team, $user, request()->all()), 201);
    }

    public function execute(AnalyticsAsset $asset, ExecuteAnalyticsAsset $action): JsonResponse
    {
        [$team,$user] = $this->context();

        return response()->json($action->execute($team, $user, $asset, request()->all()), 201);
    }
}
