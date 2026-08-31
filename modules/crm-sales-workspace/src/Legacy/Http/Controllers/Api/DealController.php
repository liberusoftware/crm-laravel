<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DealController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $sortBy = (string) $request->input('sort_by', 'created_at');
        $sortDirection = strtolower((string) $request->input('sort_direction', 'desc'));
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $query = Deal::query()
            ->byTeam($request->user()?->currentTeam?->id)
            ->when($search !== '', function (Builder $query) use ($search): void {
                $like = '%'.addcslashes($search, '\\%_').'%';
                $query->where(function (Builder $searchQuery) use ($like): void {
                    $searchQuery->where('name', 'like', $like)->orWhere('stage', 'like', $like);
                });
            })
            ->when($request->filled('stage'), fn (Builder $query): Builder => $query->where('stage', $request->string('stage')->toString()))
            ->when($request->filled('pipeline_id'), fn (Builder $query): Builder => $query->where('pipeline_id', (int) $request->input('pipeline_id')))
            ->when($request->filled('user_id'), fn (Builder $query): Builder => $query->where('user_id', (int) $request->input('user_id')));

        $sortBy = in_array($sortBy, ['created_at', 'name', 'value', 'stage', 'close_date', 'probability'], true)
            ? $sortBy
            : 'created_at';
        $sortDirection = in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'desc';

        return $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function store(Request $request)
    {
        $teamId = $request->user()?->currentTeam?->id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|numeric',
            'stage' => 'nullable|string|max:255',
            'close_date' => 'nullable|date',
            'probability' => 'nullable|integer|min:0|max:100',
            'contact_id' => ['nullable', 'integer', Rule::exists('contacts', 'id')->where('team_id', $teamId)],
            'user_id' => ['nullable', 'integer', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'pipeline_id' => ['nullable', 'integer', Rule::exists('pipelines', 'id')->where('team_id', $teamId)],
            'stage_id' => ['nullable', 'integer', Rule::exists('stages', 'id')->where('team_id', $teamId)],
        ]);

        $validated['team_id'] = $teamId;
        $deal = Deal::create($validated);

        return response()->json($deal, 201);
    }

    public function show(Request $request, Deal $deal): Deal
    {
        abort_unless($deal->belongsToTeam($request->user()?->currentTeam?->id), 403);

        return $deal;
    }

    public function update(Request $request, Deal $deal)
    {
        $teamId = $request->user()?->currentTeam?->id;
        abort_unless($deal->belongsToTeam($teamId), 403);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'value' => 'numeric',
            'stage' => 'string|max:255',
            'close_date' => 'nullable|date',
            'probability' => 'nullable|integer|min:0|max:100',
            'contact_id' => ['nullable', 'integer', Rule::exists('contacts', 'id')->where('team_id', $teamId)],
            'user_id' => ['nullable', 'integer', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'pipeline_id' => ['nullable', 'integer', Rule::exists('pipelines', 'id')->where('team_id', $teamId)],
            'stage_id' => ['nullable', 'integer', Rule::exists('stages', 'id')->where('team_id', $teamId)],
        ]);

        $deal->update($validated);

        return response()->json($deal, 200);
    }

    public function destroy(Request $request, Deal $deal)
    {
        abort_unless($deal->belongsToTeam($request->user()?->currentTeam?->id), 403);

        $deal->delete();

        return response()->json(null, 204);
    }

    /**
     * Bulk update deals.
     *
     * Expects: { "ids": [1,2,3], "data": { "status": "won", ... } }
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:deals,id',
            'data' => 'required|array',
            'data.stage' => 'sometimes|string|max:255',
            'data.stage_id' => 'sometimes|integer|exists:stages,id',
        ]);

        // 'stage' is the real string column on deals; there is no 'status'
        // column (that lives on contacts), so updating it 500s.
        $allowedFields = ['stage', 'stage_id', 'pipeline_id'];
        $updateData = array_intersect_key($request->input('data'), array_flip($allowedFields));

        if ($updateData === []) {
            return response()->json(['message' => 'No valid fields to update.'], 422);
        }

        $query = Deal::whereIn('id', $request->input('ids'));
        $query->byTeam($request->user()?->currentTeam?->id);
        $count = $query->update($updateData);

        return response()->json(['updated' => $count]);
    }

    /**
     * Bulk delete deals.
     *
     * Expects: { "ids": [1,2,3] }
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:deals,id',
        ]);

        $query = Deal::whereIn('id', $request->input('ids'));
        $query->byTeam($request->user()?->currentTeam?->id);
        $count = $query->delete();

        return response()->json(['deleted' => $count]);
    }

    /**
     * Bulk assign deals to a user.
     *
     * Expects: { "ids": [1,2,3], "user_id": 5 }
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:deals,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        // Assignee must be a member of the caller's current team, else this
        // leaks records across tenants. Refuse before touching any record.
        $team = $request->user()?->currentTeam;
        $assignee = User::find($request->input('user_id'));
        abort_unless($team && $assignee?->belongsToTeam($team), 403);

        $query = Deal::whereIn('id', $request->input('ids'));
        $query->byTeam($team->id);
        $count = $query->update(['user_id' => $request->input('user_id')]);

        return response()->json(['assigned' => $count]);
    }
}
