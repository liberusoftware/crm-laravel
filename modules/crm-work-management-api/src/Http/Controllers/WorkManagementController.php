<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\WorkManagement\Actions\AddChecklistItem;
use Liberu\CRM\WorkManagement\Actions\AddDependency;
use Liberu\CRM\WorkManagement\Actions\CompleteWorkItem;
use Liberu\CRM\WorkManagement\Actions\CreateWorkItem;
use Liberu\CRM\WorkManagement\Actions\CreateWorkQueue;
use Liberu\CRM\WorkManagement\Actions\ReviewApproval;
use Liberu\CRM\WorkManagement\Actions\UpdateWorkItem;
use Liberu\CRM\WorkManagement\Models\WorkItem;
use Liberu\CRM\WorkManagement\Models\WorkQueue;
use Liberu\CRM\WorkManagement\Services\WorkloadQuery;
use Liberu\Foundation\ApiAccess\Support\IdempotencyStore;

final class WorkManagementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = $this->owned($request)->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))->when($request->query('assigned_to'), fn ($query, $user) => $query->where('assigned_to', $user))->latest()->paginate(min(max((int) $request->query('page[size]', 25), 1), 100));

        return response()->json(['data' => $items->through(fn (WorkItem $item): array => $this->resource($item)), 'meta' => ['current_page' => $items->currentPage(), 'last_page' => $items->lastPage()], 'links' => ['self' => $request->fullUrl()]]);
    }

    public function store(Request $request, CreateWorkItem $create, IdempotencyStore $idempotency): JsonResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:200'], 'description' => ['nullable', 'string'], 'assigned_to' => ['nullable', 'integer', 'min:1'], 'queue_id' => ['nullable', 'integer', 'min:1'], 'priority' => ['nullable', 'in:low,normal,high,urgent'], 'subject_type' => ['nullable', 'string', 'max:160'], 'subject_id' => ['nullable', 'integer', 'min:1'], 'due_at' => ['nullable', 'date'], 'recurrence' => ['nullable', 'string', 'max:80'], 'next_run_at' => ['nullable', 'date'], 'metadata' => ['nullable', 'array']]);
        $replay = $this->replayIdempotent($request, $idempotency);
        if ($replay !== null) {
            return $replay;
        }
        $item = $create->execute($this->teamId($request), $request->user()->getKey(), $data);

        return $this->completeIdempotent($request, $idempotency, response()->json(['data' => $this->resource($item)], 201));
    }

    public function show(Request $request, int $workItem): JsonResponse
    {
        return response()->json(['data' => $this->resource($this->owned($request)->findOrFail($workItem))]);
    }

    public function update(Request $request, int $workItem, UpdateWorkItem $update): JsonResponse
    {
        $item = $this->owned($request)->findOrFail($workItem);
        $data = $request->validate(['title' => ['sometimes', 'string', 'max:200'], 'description' => ['nullable', 'string'], 'assigned_to' => ['nullable', 'integer', 'min:1'], 'queue_id' => ['nullable', 'integer', 'min:1'], 'status' => ['sometimes', 'in:pending,in_progress,blocked,completed,cancelled'], 'priority' => ['sometimes', 'in:low,normal,high,urgent'], 'due_at' => ['nullable', 'date'], 'metadata' => ['nullable', 'array']]);
        $updated = $update->execute($item, $request->user()->getKey(), $data, $this->expectedVersion($request));

        return response()->json(['data' => $this->resource($updated)]);
    }

    public function complete(Request $request, int $workItem, CompleteWorkItem $complete, IdempotencyStore $idempotency): JsonResponse
    {
        $replay = $this->replayIdempotent($request, $idempotency);
        if ($replay !== null) {
            return $replay;
        }
        $item = $complete->execute($this->owned($request)->findOrFail($workItem), $request->user()->getKey(), $this->expectedVersion($request));

        return $this->completeIdempotent($request, $idempotency, response()->json(['data' => $this->resource($item)]));
    }

    public function checklist(Request $request, int $workItem, AddChecklistItem $add): JsonResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:200']]);
        $check = $add->execute($this->owned($request)->findOrFail($workItem), $request->user()->getKey(), $data['title']);

        return response()->json(['data' => ['id' => (string) $check->getKey(), 'type' => 'crm-work-checklist-item', 'attributes' => $check->only(['work_item_id', 'title', 'completed', 'position'])]], 201);
    }

    public function requestApproval(Request $request, int $workItem, ReviewApproval $approval, IdempotencyStore $idempotency): JsonResponse
    {
        $replay = $this->replayIdempotent($request, $idempotency);
        if ($replay !== null) {
            return $replay;
        }
        $data = $request->validate(['comment' => ['nullable', 'string', 'max:2000']]);
        $record = $approval->request($this->owned($request)->findOrFail($workItem), $request->user()->getKey(), $data['comment'] ?? null);

        return $this->completeIdempotent($request, $idempotency, response()->json(['data' => ['id' => (string) $record->getKey(), 'type' => 'crm-work-approval', 'attributes' => $record->only(['work_item_id', 'status', 'comment', 'created_at'])]], 201));
    }

    public function dependency(Request $request, int $workItem, AddDependency $add): JsonResponse
    {
        $data = $request->validate(['depends_on_id' => ['required', 'integer', 'min:1']]);
        $item = $this->owned($request)->findOrFail($workItem);
        $dependency = $add->execute($item, $this->owned($request)->findOrFail($data['depends_on_id']), $request->user()->getKey());

        return response()->json(['data' => ['id' => (string) $dependency->getKey(), 'type' => 'crm-work-dependency', 'attributes' => $dependency->only(['work_item_id', 'depends_on_id'])]], 201);
    }

    public function queues(Request $request): JsonResponse
    {
        return response()->json(['data' => WorkQueue::query()->where('team_id', $this->teamId($request))->latest()->get()->map(fn (WorkQueue $queue): array => ['id' => (string) $queue->getKey(), 'type' => 'crm-work-queue', 'attributes' => $queue->only(['name', 'description', 'status', 'rules'])])]);
    }

    public function storeQueue(Request $request, CreateWorkQueue $create, IdempotencyStore $idempotency): JsonResponse
    {
        $replay = $this->replayIdempotent($request, $idempotency);
        if ($replay !== null) {
            return $replay;
        }
        $data = $request->validate(['name' => ['required', 'string', 'max:160'], 'description' => ['nullable', 'string', 'max:500'], 'rules' => ['nullable', 'array']]);
        $queue = $create->execute($this->teamId($request), $request->user()->getKey(), $data);

        return $this->completeIdempotent($request, $idempotency, response()->json(['data' => ['id' => (string) $queue->getKey(), 'type' => 'crm-work-queue', 'attributes' => $queue->only(['name', 'description', 'status', 'rules'])]], 201));
    }

    public function workload(Request $request, WorkloadQuery $workload): JsonResponse
    {
        return response()->json(['data' => $workload->forTeam($this->teamId($request))]);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403, 'A current team is required.');

        return (int) $teamId;
    }

    private function owned(Request $request)
    {
        return WorkItem::query()->where('team_id', $this->teamId($request));
    }

    private function expectedVersion(Request $request): ?int
    {
        $header = $request->header('If-Match');
        if ($header === null) {
            return null;
        }
        $version = trim($header, '" W/');
        abort_unless(ctype_digit($version), 409, 'If-Match must contain a work item version.');

        return (int) $version;
    }

    private function replayIdempotent(Request $request, IdempotencyStore $idempotency): ?JsonResponse
    {
        $key = $request->header('Idempotency-Key');
        if ($key === null) {
            return null;
        }
        abort_unless(strlen($key) <= 128 && trim($key) !== '', 422, 'Idempotency-Key must be a non-empty value of 128 characters or fewer.');
        $existing = $idempotency->begin((string) $request->user()->getKey(), $key, (string) $request->getContent());
        if ($existing === null) {
            return null;
        }
        abort_if($existing->response_body === null, 409, 'The idempotent request is still being processed.');

        return response()->json(json_decode($existing->response_body, true, 512, JSON_THROW_ON_ERROR), (int) $existing->response_status);
    }

    private function completeIdempotent(Request $request, IdempotencyStore $idempotency, JsonResponse $response): JsonResponse
    {
        $key = $request->header('Idempotency-Key');
        if ($key !== null) {
            $idempotency->complete((string) $request->user()->getKey(), $key, $response->getStatusCode(), (string) $response->getContent());
        }

        return $response;
    }

    /** @return array<string, mixed> */
    private function resource(WorkItem $item): array
    {
        return ['id' => (string) $item->getKey(), 'type' => 'crm-work-item', 'attributes' => $item->only(['title', 'description', 'status', 'priority', 'assigned_to', 'queue_id', 'subject_type', 'subject_id', 'due_at', 'recurrence', 'next_run_at', 'version', 'metadata', 'created_at', 'updated_at'])];
    }
}
