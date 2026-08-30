<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\CustomerDataModel\Actions\CreateObject;
use Liberu\CRM\CustomerDataModel\Actions\PublishSchema;
use Liberu\CRM\CustomerDataModel\Models\ObjectDefinition;

final class ObjectDefinitionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $objects = ObjectDefinition::query()->where('team_id', (int) $request->user()->current_team_id)->with(['fields', 'layouts'])->paginate(min(max((int) $request->query('page[size]', 25), 1), 100));

        return response()->json(['data' => $objects->through(fn (ObjectDefinition $object): array => $this->resource($object)), 'meta' => ['current_page' => $objects->currentPage(), 'last_page' => $objects->lastPage()]]);
    }

    public function store(Request $request, CreateObject $create): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'alpha_dash', 'max:80'], 'label' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'is_standard' => ['boolean']]);
        $object = $create->execute((int) $request->user()->current_team_id, $data);

        return response()->json(['data' => $this->resource($object)], 201);
    }

    public function show(Request $request, int $object): JsonResponse
    {
        $model = $this->owned($request, $object)->load(['fields', 'layouts', 'versions']);

        return response()->json(['data' => $this->resource($model)]);
    }

    public function update(Request $request, int $object): JsonResponse
    {
        $model = $this->owned($request, $object);
        abort_if($model->status === 'published', 409, 'Published schemas require a new draft version.');
        $model->update($request->validate(['label' => ['sometimes', 'string', 'max:255'], 'description' => ['nullable', 'string']]));

        return response()->json(['data' => $this->resource($model->refresh())]);
    }

    public function publish(Request $request, int $object, PublishSchema $publish): JsonResponse
    {
        $version = $publish->execute($this->owned($request, $object), $request->user()->getKey());

        return response()->json(['data' => ['id' => (string) $version->getKey(), 'type' => 'schema-version', 'attributes' => $version->only(['version', 'status', 'snapshot', 'published_at'])]], 201);
    }

    private function owned(Request $request, int $id): ObjectDefinition
    {
        return ObjectDefinition::query()->where('team_id', (int) $request->user()->current_team_id)->findOrFail($id);
    }

    /** @return array<string, mixed> */
    private function resource(ObjectDefinition $object): array
    {
        return ['id' => (string) $object->getKey(), 'type' => 'crm-customer-data-model', 'attributes' => $object->only(['key', 'label', 'description', 'is_standard', 'status', 'current_version']), 'relationships' => ['fields' => $object->fields->map(fn ($field): array => $field->only(['key', 'label', 'type', 'config', 'is_required', 'is_calculated', 'required_stages', 'position']))->all(), 'layouts' => $object->layouts->map(fn ($layout): array => $layout->only(['key', 'label', 'sections', 'is_default']))->all()]];
    }
}
