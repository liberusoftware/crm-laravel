<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\DataOperations\Actions\CreateDataOperation;
use Liberu\CRM\DataOperations\Actions\TransitionDataOperation;
use Liberu\CRM\DataOperations\Models\DataOperation;

final class DataOperationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $this->teamQuery($request)->withCount(['mappings', 'exceptions', 'duplicates']);
        $operations = $query->latest()->paginate(min(max((int) $request->query('page[size]', 25), 1), 100));

        return response()->json(['data' => $operations->through(fn (DataOperation $operation): array => $this->resource($operation)), 'meta' => ['current_page' => $operations->currentPage(), 'last_page' => $operations->lastPage()], 'links' => ['self' => $request->fullUrl()]]);
    }

    public function store(Request $request, CreateDataOperation $create): JsonResponse
    {
        $data = $request->validate(['kind' => ['required', 'in:import,export,enrichment,deduplication,formatting,quality'], 'source' => ['nullable', 'string', 'max:255'], 'target' => ['nullable', 'string', 'max:255'], 'options' => ['nullable', 'array'], 'mappings' => ['nullable', 'array', 'max:200'], 'mappings.*.source_field' => ['required_with:mappings', 'string', 'max:255'], 'mappings.*.target_field' => ['required_with:mappings', 'string', 'max:255'], 'mappings.*.transform' => ['nullable', 'in:trim,lowercase,uppercase']]);
        $mappings = $data['mappings'] ?? [];
        unset($data['mappings']);
        $operation = $create->execute((int) $request->user()->current_team_id, $request->user()->getKey(), $data, $mappings);

        return response()->json(['data' => $this->resource($operation)], 201);
    }

    public function show(Request $request, int $operation): JsonResponse
    {
        return response()->json(['data' => $this->resource($this->owned($request, $operation)->load(['mappings', 'exceptions', 'duplicates']))]);
    }

    public function update(Request $request, int $operation): JsonResponse
    {
        $model = $this->owned($request, $operation);
        abort_if(! in_array($model->status, ['draft', 'failed', 'partial'], true), 409, 'Only draft or recoverable operations can be updated.');
        $model->update($request->validate(['source' => ['sometimes', 'nullable', 'string', 'max:255'], 'target' => ['sometimes', 'nullable', 'string', 'max:255'], 'options' => ['sometimes', 'nullable', 'array']]));

        return response()->json(['data' => $this->resource($model->refresh())]);
    }

    public function transition(Request $request, int $operation, TransitionDataOperation $transition): JsonResponse
    {
        $data = $request->validate(['status' => ['required', 'in:queued,running,completed,partial,failed'], 'reason' => ['nullable', 'string', 'max:2000']]);

        return response()->json(['data' => $this->resource($transition->execute($this->owned($request, $operation), $data['status'], $data['reason'] ?? null))]);
    }

    private function owned(Request $request, int $id): DataOperation
    {
        return $this->teamQuery($request)->findOrFail($id);
    }

    private function teamQuery(Request $request)
    {
        return DataOperation::query()->where('team_id', (int) $request->user()->current_team_id);
    }

    /** @return array<string, mixed> */
    private function resource(DataOperation $operation): array
    {
        return ['id' => (string) $operation->getKey(), 'type' => 'crm-data-operation', 'attributes' => $operation->only(['kind', 'status', 'source', 'target', 'options', 'total_rows', 'processed_rows', 'failed_rows', 'failure_reason', 'created_at', 'started_at', 'completed_at']), 'meta' => ['mappings_count' => $operation->mappings_count ?? $operation->mappings->count(), 'exceptions_count' => $operation->exceptions_count ?? $operation->exceptions->count(), 'duplicates_count' => $operation->duplicates_count ?? $operation->duplicates->count()]];
    }
}
