<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\AccountBasedMarketing\Actions\TransitionRecord;
use Liberu\CRM\AccountBasedMarketing\Actions\UpsertRecord;
use Liberu\CRM\AccountBasedMarketing\Models\AccountBasedMarketingRecord;
use Liberu\CRM\AccountBasedMarketing\Queries\AccountBasedMarketingQuery;

final class AccountBasedMarketingController extends Controller
{
    public function __construct(private readonly AccountBasedMarketingQuery $query) {}

    public function index(Request $request): JsonResponse
    {
        $records = $this->query->records($this->teamId(), $request->string('kind')->toString())
            ->paginate(min(max($request->integer('per_page', 25), 1), 100));

        return response()->json($records);
    }

    public function store(Request $request, UpsertRecord $upsert): JsonResponse
    {
        $record = $upsert->execute($this->teamId(), $this->validated($request));

        return response()->json(['data' => $record], 201);
    }

    public function update(Request $request, int $record, UpsertRecord $upsert): JsonResponse
    {
        $recordModel = AccountBasedMarketingRecord::query()->forTeam($this->teamId())->findOrFail($record);
        $updated = $upsert->execute($this->teamId(), $this->validated($request, false), $recordModel->getKey());

        return response()->json(['data' => $updated]);
    }

    public function transition(Request $request, int $record, TransitionRecord $transition): JsonResponse
    {
        $data = $request->validate(['status' => ['required', 'in:'.implode(',', AccountBasedMarketingRecord::STATUSES)]]);

        return response()->json(['data' => $transition->execute($this->teamId(), $record, $data['status'])]);
    }

    private function teamId(): int
    {
        abort_unless($this->requestUser()->current_team_id, 403);

        return (int) $this->requestUser()->current_team_id;
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, bool $create = true): array
    {
        return $request->validate([
            'kind' => [$create ? 'required' : 'sometimes', 'string', 'in:'.implode(',', AccountBasedMarketingRecord::KINDS)],
            'name' => [$create ? 'required' : 'sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:'.implode(',', AccountBasedMarketingRecord::STATUSES)],
            'account_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'owner_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'payload' => ['sometimes', 'nullable', 'array'],
            'starts_at' => ['sometimes', 'nullable', 'date'],
            'ends_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:starts_at'],
        ]);
    }

    private function requestUser(): object
    {
        return request()->user();
    }
}
