<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\Core\Actions\AddNote;
use Liberu\CRM\Core\Actions\ArchiveRecord;
use Liberu\CRM\Core\Actions\CreateRecord;
use Liberu\CRM\Core\Actions\CreateRelationship;
use Liberu\CRM\Core\Actions\CreateTag;
use Liberu\CRM\Core\Actions\TagRecord;
use Liberu\CRM\Core\Actions\ToggleFavorite;
use Liberu\CRM\Core\Actions\UpdateRecord;
use Liberu\CRM\Core\Enums\RecordType;
use Liberu\CRM\Core\Models\Record;
use Liberu\CRM\Core\Models\Tag;

final class RecordController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $this->teamId($request);
        $type = (string) $request->query('type', RecordType::Contact->value);
        abort_if(RecordType::tryFrom($type) === null, 422, 'The record type is not supported.');
        $pageSize = max(1, min((int) $request->query('page[size]', 25), 100));
        $records = Record::query()->where('team_id', $teamId)->where('record_type', $type)->active()->paginate($pageSize);

        return response()->json(['data' => $records->through(fn (Record $record): array => $this->resource($record)), 'meta' => ['current_page' => $records->currentPage(), 'last_page' => $records->lastPage()]]);
    }

    public function store(Request $request, CreateRecord $create): JsonResponse
    {
        $data = $request->validate(['type' => ['required', 'string', 'in:contact,organization,household'], 'name' => ['required', 'string', 'max:255'], 'data' => ['sometimes', 'array'], 'owner_id' => ['nullable', 'integer', 'min:1']]);
        $record = $create->execute($data['type'], $this->teamId($request), $data['name'], $data['data'] ?? [], $data['owner_id'] ?? null);

        return response()->json(['data' => $this->resource($record)], 201);
    }

    public function show(Request $request, int $record): JsonResponse
    {
        $model = $this->record($request, $record);

        return response()->json(['data' => $this->resource($model)]);
    }

    public function tags(Request $request): JsonResponse
    {
        return response()->json(['data' => Tag::query()->where('team_id', $this->teamId($request))->latest('id')->paginate(100)->through(fn (Tag $tag): array => ['id' => (string) $tag->getKey(), 'type' => 'crm-core-tag', 'attributes' => $tag->only(['name', 'slug'])])]);
    }

    public function createTag(Request $request, CreateTag $create): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);
        $tag = $create->execute($this->teamId($request), $data['name']);

        return response()->json(['data' => ['id' => (string) $tag->getKey(), 'type' => 'crm-core-tag', 'attributes' => $tag->only(['name', 'slug'])]], 201);
    }

    public function addNote(Request $request, int $record, AddNote $add): JsonResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:10000']]);
        $note = $add->execute($this->record($request, $record), $data['body'], (int) $request->user()->getAuthIdentifier());

        return response()->json(['data' => ['id' => (string) $note->getKey(), 'type' => 'crm-core-note', 'attributes' => $note->only(['body', 'author_id', 'created_at'])]], 201);
    }

    public function timeline(Request $request, int $record): JsonResponse
    {
        $timeline = $this->record($request, $record)->timeline()->paginate(25);

        return response()->json(['data' => $timeline->through(fn ($entry): array => ['id' => (string) $entry->getKey(), 'type' => 'crm-core-timeline-entry', 'attributes' => $entry->only(['event_type', 'summary', 'payload', 'actor_id', 'created_at'])])]);
    }

    public function relationship(Request $request, int $record, CreateRelationship $create): JsonResponse
    {
        $data = $request->validate(['to_id' => ['required', 'integer', 'min:1'], 'relationship_type' => ['required', 'string', 'max:80'], 'metadata' => ['sometimes', 'array']]);
        $from = $this->record($request, $record);
        $to = Record::query()->where('team_id', $this->teamId($request))->findOrFail($data['to_id']);
        $relationship = $create->execute($from, $to, $data['relationship_type'], $data['metadata'] ?? []);

        return response()->json(['data' => ['id' => (string) $relationship->getKey(), 'type' => 'crm-core-relationship', 'attributes' => $relationship->only(['relationship_type', 'metadata', 'created_at'])]], 201);
    }

    public function tag(Request $request, int $record, int $tag, TagRecord $attach): JsonResponse
    {
        $model = $this->record($request, $record);
        $tagModel = Tag::query()->where('team_id', $this->teamId($request))->findOrFail($tag);
        $attach->execute($model, $tagModel);

        return response()->json(['data' => ['attached' => true]]);
    }

    public function favorite(Request $request, int $record, ToggleFavorite $toggle): JsonResponse
    {
        $favorite = $toggle->execute($this->record($request, $record), $this->teamId($request), (int) $request->user()->getAuthIdentifier());

        return response()->json(['data' => ['favorited' => $favorite]]);
    }

    public function update(Request $request, int $record, UpdateRecord $update): JsonResponse
    {
        $model = $this->record($request, $record);
        $data = $request->validate(['name' => ['sometimes', 'string', 'max:255'], 'status' => ['sometimes', 'string', 'in:active,archived'], 'data' => ['sometimes', 'array'], 'owner_id' => ['sometimes', 'nullable', 'integer', 'min:1']]);
        $model = $update->execute($model, $data);

        return response()->json(['data' => $this->resource($model->refresh())]);
    }

    public function destroy(Request $request, int $record, ArchiveRecord $archive): JsonResponse
    {
        $model = $this->record($request, $record);
        $archive->execute($model);

        return response()->json(status: 204);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403, 'A current team is required.');

        return (int) $teamId;
    }

    private function record(Request $request, int $id): Record
    {
        return Record::query()->where('team_id', $this->teamId($request))->findOrFail($id);
    }

    /** @return array<string, mixed> */
    private function resource(Record $record): array
    {
        return ['id' => (string) $record->getKey(), 'type' => 'crm-core', 'attributes' => ['record_type' => $record->record_type, 'name' => $record->name, 'status' => $record->status, 'data' => $record->data, 'created_at' => $record->created_at?->toISOString(), 'updated_at' => $record->updated_at?->toISOString()]];
    }
}
