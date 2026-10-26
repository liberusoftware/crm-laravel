<?php

declare(strict_types=1);

namespace Liberu\CRM\ForecastingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\Forecasting\Actions\AdjustForecast;
use Liberu\CRM\Forecasting\Actions\CreateCategory;
use Liberu\CRM\Forecasting\Actions\RecordForecast;
use Liberu\CRM\Forecasting\Actions\SubmitForecast;
use Liberu\CRM\Forecasting\Models\Forecast;
use Liberu\CRM\Forecasting\Queries\ForecastingQuery;

final class ForecastingController extends Controller
{
    public function __construct(private readonly ForecastingQuery $query) {}

    public function categories(): JsonResponse
    {
        $user = request()->user();

        return response()->json($this->query->categories((int) $user->getAttribute('current_team_id'))->get());
    }

    public function index(string $period): JsonResponse
    {
        $user = request()->user();

        return response()->json(['data' => $this->query->forecasts((int) $user->getAttribute('current_team_id'), $period)->get(), 'summary' => $this->query->summary((int) $user->getAttribute('current_team_id'), $period)]);
    }

    public function storeCategory(CreateCategory $action): JsonResponse
    {
        $user = request()->user();

        return response()->json($action->execute((int) $user->getAttribute('current_team_id'), (int) $user->id, request()->all()), 201);
    }

    public function store(RecordForecast $action): JsonResponse
    {
        $user = request()->user();

        return response()->json($action->execute((int) $user->getAttribute('current_team_id'), (int) $user->id, request()->all()), 201);
    }

    public function submit(int $forecast, SubmitForecast $action): JsonResponse
    {
        $user = request()->user();
        $model = Forecast::query()->where('team_id', (int) $user->getAttribute('current_team_id'))->findOrFail($forecast);

        return response()->json($action->execute((int) $user->getAttribute('current_team_id'), (int) $user->id, $model), 201);
    }

    public function adjust(int $forecast, AdjustForecast $action): JsonResponse
    {
        $user = request()->user();

        return response()->json($action->execute((int) $user->getAttribute('current_team_id'), (int) $user->id, $forecast, request()->all()), 201);
    }
}
