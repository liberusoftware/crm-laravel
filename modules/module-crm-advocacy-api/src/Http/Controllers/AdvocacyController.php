<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvocacyApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\Advocacy\Actions\TransitionRecord;
use Liberu\CRM\Advocacy\Actions\UpsertRecord;
use Liberu\CRM\Advocacy\Models\AdvocacyRecord;
use Liberu\CRM\Advocacy\Queries\AdvocacyQuery;

final class AdvocacyController extends Controller
{
    public function __construct(private readonly AdvocacyQuery $query) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->query->records($this->teamId(), $request->string('kind')->toString())->paginate(min(max($request->integer('per_page', 25), 1), 100)));
    }

    public function store(Request $request, UpsertRecord $upsert): JsonResponse
    {
        return response()->json(['data' => $upsert->execute($this->teamId(), $this->validated($request))], 201);
    }

    public function update(Request $request, int $record, UpsertRecord $upsert): JsonResponse
    {
        AdvocacyRecord::query()->forTeam($this->teamId())->findOrFail($record);

        return response()->json(['data' => $upsert->execute($this->teamId(), $this->validated($request, false), $record)]);
    }

    public function transition(Request $request, int $record, TransitionRecord $transition): JsonResponse
    {
        $data = $request->validate(['status' => ['required', 'in:'.implode(',', AdvocacyRecord::STATUSES)]]);

        return response()->json(['data' => $transition->execute($this->teamId(), $record, $data['status'])]);
    }

    private function teamId(): int
    {
        abort_unless((bool) request()->user()?->current_team_id, 403);

        return (int) request()->user()->current_team_id;
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, bool $create = true): array
    {
        return $request->validate(['kind' => [$create ? 'required' : 'sometimes', 'string', 'in:'.implode(',', AdvocacyRecord::KINDS)], 'name' => [$create ? 'required' : 'sometimes', 'string', 'max:255'], 'status' => ['sometimes', 'string', 'in:'.implode(',', AdvocacyRecord::STATUSES)], 'contact_id' => ['sometimes', 'nullable', 'integer', 'min:1'], 'owner_id' => ['sometimes', 'nullable', 'integer', 'min:1'], 'payload' => ['sometimes', 'nullable', 'array'], 'requested_at' => ['sometimes', 'nullable', 'date']]);
    }
}
