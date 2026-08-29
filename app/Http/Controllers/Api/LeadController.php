<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $sortBy = (string) $request->input('sort_by', 'created_at');
        $sortDirection = strtolower((string) $request->input('sort_direction', 'desc'));
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $query = Lead::query()
            ->byTeam($this->teamId($request))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $like = '%'.addcslashes($search, '\\%_').'%';
                $query->where(function (Builder $searchQuery) use ($like): void {
                    $searchQuery
                        ->where('status', 'like', $like)
                        ->orWhere('source', 'like', $like)
                        ->orWhere('lifecycle_stage', 'like', $like);
                });
            })
            ->when($request->filled('status'), fn (Builder $query): Builder => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('source'), fn (Builder $query): Builder => $query->where('source', $request->string('source')->toString()))
            ->when($request->filled('lifecycle_stage'), fn (Builder $query): Builder => $query->where('lifecycle_stage', $request->string('lifecycle_stage')->toString()))
            ->when($request->filled('user_id'), fn (Builder $query): Builder => $query->where('user_id', (int) $request->input('user_id')))
            ->when($request->filled('potential_value_min'), fn (Builder $query): Builder => $query->where('potential_value', '>=', $request->input('potential_value_min')))
            ->when($request->filled('potential_value_max'), fn (Builder $query): Builder => $query->where('potential_value', '<=', $request->input('potential_value_max')));

        $sortBy = in_array($sortBy, ['created_at', 'status', 'source', 'lifecycle_stage', 'potential_value', 'expected_close_date', 'score'], true)
            ? $sortBy
            : 'created_at';
        $sortDirection = in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'desc';

        return $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function store(Request $request)
    {
        $teamId = $this->teamId($request);

        $validated = $request->validate([
            'status' => 'nullable|string|in:new,contacted,qualified,lost',
            'source' => 'nullable|string|max:255',
            'potential_value' => 'nullable|numeric|min:0',
            'expected_close_date' => 'nullable|date',
            'contact_id' => ['nullable', 'integer', Rule::exists('contacts', 'id')->where('team_id', $teamId)],
            'user_id' => ['nullable', 'integer', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'lifecycle_stage' => ['nullable', Rule::in(Lead::LIFECYCLE_STAGES)],
            'custom_fields' => 'nullable|array',
        ]);

        $validated['team_id'] = $teamId;
        $lead = new Lead($validated);
        $lead->save();
        $lead->calculateScore();

        return response()->json($lead->refresh(), 201);
    }

    public function show(Request $request, Lead $lead): Lead
    {
        abort_unless($lead->belongsToTeam($this->teamId($request)), 403);

        return $lead;
    }

    public function update(Request $request, Lead $lead)
    {
        $teamId = $this->teamId($request);
        abort_unless($lead->belongsToTeam($teamId), 403);

        $validated = $request->validate([
            'status' => 'sometimes|string|in:new,contacted,qualified,lost',
            'source' => 'sometimes|nullable|string|max:255',
            'potential_value' => 'sometimes|nullable|numeric|min:0',
            'expected_close_date' => 'sometimes|nullable|date',
            'contact_id' => ['sometimes', 'nullable', 'integer', Rule::exists('contacts', 'id')->where('team_id', $teamId)],
            'user_id' => ['sometimes', 'nullable', 'integer', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'lifecycle_stage' => ['sometimes', 'nullable', Rule::in(Lead::LIFECYCLE_STAGES)],
            'custom_fields' => 'sometimes|nullable|array',
        ]);

        $lead->update($validated);
        $lead->calculateScore();

        return response()->json($lead->refresh());
    }

    public function destroy(Request $request, Lead $lead)
    {
        abort_unless($lead->belongsToTeam($this->teamId($request)), 403);

        $lead->delete();

        return response()->json(null, 204);
    }

    private function teamId(Request $request): ?int
    {
        $team = $request->user()?->currentTeam;

        if ($team === null) {
            return null;
        }

        $key = $team->getKey();

        return is_numeric($key) ? (int) $key : null;
    }
}
