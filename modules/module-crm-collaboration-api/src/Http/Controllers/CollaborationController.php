<?php

declare(strict_types=1);

namespace Liberu\CRM\CollaborationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\Collaboration\Actions\AddCollaborationRecord;
use Liberu\CRM\Collaboration\Actions\AssignCollaborationWork;
use Liberu\CRM\Collaboration\Actions\HandoffCollaborationWork;
use Liberu\CRM\Collaboration\Models\CollaborationWork;
use Liberu\CRM\Collaboration\Queries\CollaborationQuery;

final class CollaborationController extends Controller
{
    public function __construct(private readonly CollaborationQuery $query) {}

    private function team(): int
    {
        return (int) request()->user()->current_team_id;
    }

    public function records(string $recordKey): JsonResponse
    {
        return response()->json($this->query->records($this->team(), $recordKey)->get());
    }

    public function record(AddCollaborationRecord $a): JsonResponse
    {
        return response()->json($a->execute($this->team(), (string) request('record_key'), (string) request('author_key', (string) request()->user()->id), (string) request('body'), (string) request('kind', 'comment'), (array) request('mentions', [])), 201);
    }

    public function assign(AssignCollaborationWork $a): JsonResponse
    {
        return response()->json($a->execute($this->team(), (string) request('queue_key'), (string) request('subject_key'), request('assignee_key'), (array) request('metadata', [])), 201);
    }

    public function handoff(CollaborationWork $work, HandoffCollaborationWork $a): JsonResponse
    {
        return response()->json($a->execute($this->team(), $work, (string) request('assignee_key'), (string) request('reason')));
    }

    public function queue(string $queueKey): JsonResponse
    {
        return response()->json($this->query->queue($this->team(), $queueKey)->get());
    }
}
