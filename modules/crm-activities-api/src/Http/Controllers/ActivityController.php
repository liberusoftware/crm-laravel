<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\Activities\Actions\CancelActivity;
use Liberu\CRM\Activities\Actions\CompleteActivities;
use Liberu\CRM\Activities\Actions\CreateActivity;
use Liberu\CRM\Activities\Models\Activity;
use Liberu\CRM\Activities\Services\ActivityReport;

final class ActivityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $this->owned($request)->when($request->query('kind'), fn ($query, $kind) => $query->where('kind', $kind))->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))->latest();
        $activities = $query->paginate(min(max((int) $request->query('page[size]', 25), 1), 100));

        return response()->json(['data' => $activities->through(fn (Activity $activity): array => $this->resource($activity)), 'meta' => ['current_page' => $activities->currentPage(), 'last_page' => $activities->lastPage()], 'links' => ['self' => $request->fullUrl()]]);
    }

    public function store(Request $request, CreateActivity $create): JsonResponse
    {
        $data = $request->validate(['kind' => ['required', 'in:task,call,meeting,email'], 'title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'subject_type' => ['nullable', 'string', 'max:120'], 'subject_id' => ['nullable', 'integer', 'min:1'], 'assigned_to' => ['nullable', 'integer', 'min:1'], 'starts_at' => ['nullable', 'date'], 'due_at' => ['nullable', 'date'], 'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'], 'recurrence' => ['nullable', 'in:daily,weekly,monthly'], 'recurrence_until' => ['nullable', 'date', 'after:due_at'], 'reminder_at' => ['nullable', 'date'], 'queue' => ['nullable', 'string', 'max:120'], 'metadata' => ['nullable', 'array']]);
        $activity = $create->execute((int) $request->user()->current_team_id, $request->user()->getKey(), $data);

        return response()->json(['data' => $this->resource($activity)], 201);
    }

    public function show(Request $request, int $activity): JsonResponse
    {
        return response()->json(['data' => $this->resource($this->owned($request)->findOrFail($activity))]);
    }

    public function update(Request $request, int $activity): JsonResponse
    {
        $model = $this->owned($request)->findOrFail($activity);
        abort_if($model->status === 'completed', 409, 'Completed activities cannot be edited.');
        $model->update($request->validate(['title' => ['sometimes', 'string', 'max:255'], 'description' => ['sometimes', 'nullable', 'string'], 'due_at' => ['sometimes', 'nullable', 'date'], 'reminder_at' => ['sometimes', 'nullable', 'date'], 'assigned_to' => ['sometimes', 'nullable', 'integer', 'min:1'], 'metadata' => ['sometimes', 'nullable', 'array']]));

        return response()->json(['data' => $this->resource($model->refresh())]);
    }

    public function complete(Request $request, CompleteActivities $complete): JsonResponse
    {
        $data = $request->validate(['activity_ids' => ['required', 'array', 'min:1', 'max:100'], 'activity_ids.*' => ['integer', 'distinct'], 'outcome' => ['nullable', 'string', 'max:120'], 'outcome_notes' => ['nullable', 'string', 'max:5000']]);
        $count = $complete->execute((int) $request->user()->current_team_id, $data['activity_ids'], $data['outcome'] ?? null, $data['outcome_notes'] ?? null);

        return response()->json(['data' => ['completed' => $count]]);
    }

    public function cancel(Request $request, int $activity, CancelActivity $cancel): JsonResponse
    {
        return response()->json(['data' => $this->resource($cancel->execute($this->owned($request)->findOrFail($activity)))]);
    }

    public function report(Request $request, ActivityReport $report): JsonResponse
    {
        $data = $request->validate(['from' => ['required', 'date'], 'until' => ['required', 'date', 'after_or_equal:from']]);

        return response()->json(['data' => $report->summarize((int) $request->user()->current_team_id, now()->parse($data['from']), now()->parse($data['until']))]);
    }

    private function owned(Request $request)
    {
        return Activity::query()->where('team_id', (int) $request->user()->current_team_id);
    }

    /** @return array<string, mixed> */
    private function resource(Activity $activity): array
    {
        return ['id' => (string) $activity->getKey(), 'type' => 'crm-activities', 'attributes' => $activity->only(['kind', 'status', 'title', 'description', 'subject_type', 'subject_id', 'assigned_to', 'starts_at', 'due_at', 'ends_at', 'recurrence', 'recurrence_until', 'reminder_at', 'queue', 'outcome', 'outcome_notes', 'metadata', 'completed_at', 'created_at'])];
    }
}
