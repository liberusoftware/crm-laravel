<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\Core\Actions\ArchiveRecord;
use Liberu\CRM\Core\Actions\CreateRecord;
use Liberu\CRM\Core\Models\Record;

final class RecordController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teamId = (int) $request->user()->current_team_id;
        $type = (string) $request->query('type', 'contact');
        $records = Record::query()->where('team_id', $teamId)->where('record_type', $type)->active()->paginate(min((int) $request->query('page[size]', 25), 100));

        return response()->json(['data' => $records->through(fn (Record $record): array => $this->resource($record)), 'meta' => ['current_page' => $records->currentPage(), 'last_page' => $records->lastPage()]]);
    }

    public function store(Request $request, CreateRecord $create): JsonResponse
    {
        $data = $request->validate(['type' => ['required', 'string', 'max:40'], 'name' => ['required', 'string', 'max:255'], 'data' => ['array'], 'owner_id' => ['nullable', 'integer']]);
        $record = $create->execute($data['type'], (int) $request->user()->current_team_id, $data['name'], $data['data'] ?? [], $data['owner_id'] ?? null);

        return response()->json(['data' => $this->resource($record)], 201);
    }

    public function show(Request $request, int $record): JsonResponse
    {
        $model = Record::query()->where('team_id', (int) $request->user()->current_team_id)->findOrFail($record);

        return response()->json(['data' => $this->resource($model)]);
    }

    public function update(Request $request, int $record): JsonResponse
    {
        $model = Record::query()->where('team_id', (int) $request->user()->current_team_id)->findOrFail($record);
        $model->update($request->validate(['name' => ['sometimes', 'string', 'max:255'], 'status' => ['sometimes', 'string', 'max:30'], 'data' => ['sometimes', 'array'], 'owner_id' => ['nullable', 'integer']]));

        return response()->json(['data' => $this->resource($model->refresh())]);
    }

    public function destroy(Request $request, int $record, ArchiveRecord $archive): JsonResponse
    {
        $model = Record::query()->where('team_id', (int) $request->user()->current_team_id)->findOrFail($record);
        $archive->execute($model);

        return response()->json(status: 204);
    }

    /** @return array<string, mixed> */
    private function resource(Record $record): array
    {
        return ['id' => (string) $record->getKey(), 'type' => 'crm-core', 'attributes' => ['record_type' => $record->record_type, 'name' => $record->name, 'status' => $record->status, 'data' => $record->data, 'created_at' => $record->created_at?->toISOString(), 'updated_at' => $record->updated_at?->toISOString()]];
    }
}
