<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversionOptimizationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\ConversionOptimization\Actions\ActivateExperiment;
use Liberu\CRM\ConversionOptimization\Actions\CreateConversionExperiment;
use Liberu\CRM\ConversionOptimization\Actions\RecordConversion;
use Liberu\CRM\ConversionOptimization\Models\ConversionExperiment;
use Liberu\CRM\ConversionOptimization\Queries\ConversionReportQuery;

final class ConversionOptimizationController extends Controller
{
    public function __construct(private readonly ConversionReportQuery $query) {}

    private function context(): array
    {
        $u = request()->user();

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function index(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->experiments($t)->get());
    }

    public function store(CreateConversionExperiment $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, request()->all()), 201);
    }

    public function activate(ConversionExperiment $e, ActivateExperiment $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, $e));
    }

    public function convert(ConversionExperiment $e, RecordConversion $a): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($a->execute($t, $e, (string) request('subject_key'), (string) request('variant'), (string) request('event'), (float) request('value', 1)), 201);
    }

    public function report(ConversionExperiment $e): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->report($t, $e));
    }
}
