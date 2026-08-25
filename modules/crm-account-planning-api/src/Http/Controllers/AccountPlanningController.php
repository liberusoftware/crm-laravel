<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanningApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\AccountPlanning\Actions\TransitionRecord;
use Liberu\CRM\AccountPlanning\Actions\UpsertRecord;
use Liberu\CRM\AccountPlanning\Models\AccountPlanningRecord;
use Liberu\CRM\AccountPlanning\Queries\AccountPlanningQuery;

final class AccountPlanningController extends Controller
{
    public function __construct(private readonly AccountPlanningQuery $query) {}

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
        AccountPlanningRecord::query()->forTeam($this->teamId())->findOrFail($record);

        return response()->json(['data' => $upsert->execute($this->teamId(), $this->validated($request, false), $record)]);
    }

    public function transition(Request $request, int $record, TransitionRecord $transition): JsonResponse
    {
        $data = $request->validate(['status' => ['required', 'in:'.implode(',', AccountPlanningRecord::STATUSES)]]);

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
        return $request->validate(['kind' => [$create ? 'required' : 'sometimes', 'string', 'in:'.implode(',', AccountPlanningRecord::KINDS)], 'name' => [$create ? 'required' : 'sometimes', 'string', 'max:255'], 'status' => ['sometimes', 'string', 'in:'.implode(',', AccountPlanningRecord::STATUSES)], 'account_id' => ['sometimes', 'nullable', 'integer', 'min:1'], 'owner_id' => ['sometimes', 'nullable', 'integer', 'min:1'], 'payload' => ['sometimes', 'nullable', 'array'], 'starts_at' => ['sometimes', 'nullable', 'date'], 'ends_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:starts_at']]);
    }
}
