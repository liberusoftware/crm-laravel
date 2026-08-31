<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvertisingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\Advertising\Actions\TransitionRecord;
use Liberu\CRM\Advertising\Actions\UpsertRecord;
use Liberu\CRM\Advertising\Models\AdvertisingRecord;
use Liberu\CRM\Advertising\Queries\AdvertisingQuery;
use Liberu\CRM\AdvertisingApi\Http\Resources\AdvertisingResource;

final class AdvertisingController extends Controller
{
    public function __construct(private readonly AdvertisingQuery $query) {}

    public function index(Request $request): JsonResponse
    {
        return AdvertisingResource::collection($this->query->records($this->teamId(), $request->string('kind')->toString())->paginate(min(max($request->integer('per_page', 25), 1), 100)))->response();
    }

    public function store(Request $request, UpsertRecord $upsert): JsonResponse
    {
        return (new AdvertisingResource($upsert->execute($this->teamId(), $this->validated($request))))->response()->setStatusCode(201);
    }

    public function show(int $record): AdvertisingResource
    {
        return new AdvertisingResource(AdvertisingRecord::query()->forTeam($this->teamId())->findOrFail($record));
    }

    public function update(Request $request, int $record, UpsertRecord $upsert): JsonResponse
    {
        AdvertisingRecord::query()->forTeam($this->teamId())->findOrFail($record);

        return new AdvertisingResource($upsert->execute($this->teamId(), $this->validated($request, false), $record));
    }

    public function transition(Request $request, int $record, TransitionRecord $transition): JsonResponse
    {
        $data = $request->validate(['status' => ['required', 'in:'.implode(',', AdvertisingRecord::STATUSES)]]);

        return new AdvertisingResource($transition->execute($this->teamId(), $record, $data['status']));
    }

    public function destroy(int $record, TransitionRecord $transition): JsonResponse
    {
        $transition->execute($this->teamId(), $record, 'archived');

        return response()->json(null, 204);
    }

    private function teamId(): int
    {
        abort_unless((bool) request()->user()?->current_team_id, 403);

        return (int) request()->user()->current_team_id;
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, bool $create = true): array
    {
        return $request->validate(['kind' => [$create ? 'required' : 'sometimes', 'string', 'in:'.implode(',', AdvertisingRecord::KINDS)], 'name' => [$create ? 'required' : 'sometimes', 'string', 'max:255'], 'status' => ['sometimes', 'string', 'in:'.implode(',', AdvertisingRecord::STATUSES)], 'external_id' => ['sometimes', 'nullable', 'string', 'max:255'], 'platform' => ['sometimes', 'nullable', 'string', 'max:48'], 'payload' => ['sometimes', 'nullable', 'array'], 'starts_at' => ['sometimes', 'nullable', 'date'], 'ends_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:starts_at']]);
    }
}
