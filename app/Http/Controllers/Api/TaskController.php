<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskAssignmentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function __construct(protected TaskAssignmentService $taskAssignmentService) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $sortBy = (string) $request->input('sort_by', 'created_at');
        $sortDirection = strtolower((string) $request->input('sort_direction', 'desc'));
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $query = Task::query()
            ->byTeam($request->user()?->currentTeam?->id)
            ->when($search !== '', function (Builder $query) use ($search): void {
                $like = '%'.addcslashes($search, '\\%_').'%';
                $query->where(function (Builder $searchQuery) use ($like): void {
                    $searchQuery->where('name', 'like', $like)->orWhere('description', 'like', $like);
                });
            })
            ->when($request->filled('status'), fn (Builder $query): Builder => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('assigned_to'), fn (Builder $query): Builder => $query->where('assigned_to', (int) $request->input('assigned_to')))
            ->when($request->filled('due_from'), fn (Builder $query): Builder => $query->whereDate('due_date', '>=', $request->input('due_from')))
            ->when($request->filled('due_to'), fn (Builder $query): Builder => $query->whereDate('due_date', '<=', $request->input('due_to')));

        $sortBy = in_array($sortBy, ['created_at', 'name', 'status', 'due_date', 'reminder_date'], true)
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
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after_or_equal:today',
            'status' => 'nullable|string|in:pending,in_progress,completed',
            'recurrence' => 'nullable|string|in:daily,weekly,monthly',
            'assigned_to' => ['nullable', 'integer', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'contact_id' => ['nullable', 'integer', Rule::exists('contacts', 'id')->where('team_id', $teamId)],
            'lead_id' => ['nullable', 'integer', Rule::exists('leads', 'id')->where('team_id', $teamId)],
            'company_id' => ['nullable', 'integer', Rule::exists('companies', 'id')->where('team_id', $teamId)],
            'calendar_type' => 'nullable|string|in:google,outlook',
        ]);

        $validated['team_id'] = $teamId;
        $task = Task::create($validated);
        $this->taskAssignmentService->notify($task);

        return response()->json($task, 201);
    }

    public function show(Request $request, Task $task): Task
    {
        abort_unless($task->belongsToTeam($request->user()?->currentTeam?->id), 403);

        return $task;
    }

    public function update(Request $request, Task $task)
    {
        $teamId = $request->user()?->currentTeam?->id;
        abort_unless($task->belongsToTeam($teamId), 403);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'string|in:pending,in_progress,completed',
            'recurrence' => 'nullable|string|in:daily,weekly,monthly',
            'assigned_to' => ['nullable', 'integer', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'contact_id' => ['nullable', 'integer', Rule::exists('contacts', 'id')->where('team_id', $teamId)],
            'lead_id' => ['nullable', 'integer', Rule::exists('leads', 'id')->where('team_id', $teamId)],
            'company_id' => ['nullable', 'integer', Rule::exists('companies', 'id')->where('team_id', $teamId)],
            'calendar_type' => 'nullable|string|in:google,outlook',
        ]);

        $previousAssigneeId = $task->assigned_to;
        $task->update($validated);
        $this->taskAssignmentService->notify($task, $previousAssigneeId);

        return response()->json($task, 200);
    }

    public function destroy(Request $request, Task $task)
    {
        abort_unless($task->belongsToTeam($request->user()?->currentTeam?->id), 403);

        $task->delete();

        return response()->json(null, 204);
    }

    /**
     * Bulk update tasks.
     *
     * Expects: { "ids": [1,2,3], "data": { "status": "completed" } }
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:tasks,id',
            'data' => 'required|array',
            'data.status' => 'sometimes|string|in:pending,in_progress,completed',
            'data.due_date' => 'sometimes|nullable|date',
        ]);

        $allowedFields = ['status', 'due_date'];
        $updateData = array_intersect_key($request->input('data'), array_flip($allowedFields));

        if ($updateData === []) {
            return response()->json(['message' => 'No valid fields to update.'], 422);
        }

        $query = Task::whereIn('id', $request->input('ids'));
        $query->byTeam($request->user()?->currentTeam?->id);
        $count = $query->update($updateData);

        return response()->json(['updated' => $count]);
    }

    /**
     * Bulk delete tasks.
     *
     * Expects: { "ids": [1,2,3] }
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:tasks,id',
        ]);

        $query = Task::whereIn('id', $request->input('ids'));
        $query->byTeam($request->user()?->currentTeam?->id);
        $count = $query->delete();

        return response()->json(['deleted' => $count]);
    }

    /**
     * Bulk assign tasks to a user.
     *
     * Expects: { "ids": [1,2,3], "user_id": 5 }
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:tasks,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        // Assignee must be a member of the caller's current team, else this
        // leaks records across tenants. Refuse before touching any record.
        $team = $request->user()?->currentTeam;
        $assignee = User::find($request->input('user_id'));
        abort_unless($team && $assignee?->belongsToTeam($team), 403);

        $query = Task::whereIn('id', $request->input('ids'));
        $query->byTeam($team->id);
        // Tasks store the assignee in `assigned_to` (there is no user_id column).
        $tasks = $query->get();
        foreach ($tasks as $task) {
            $previousAssigneeId = $task->assigned_to;
            $task->update(['assigned_to' => $request->input('user_id')]);
            $this->taskAssignmentService->notify($task, $previousAssigneeId);
        }
        $count = $tasks->count();

        return response()->json(['assigned' => $count]);
    }
}
