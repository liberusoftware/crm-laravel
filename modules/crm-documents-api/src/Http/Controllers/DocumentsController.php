<?php

declare(strict_types=1);

namespace Liberu\CRM\DocumentsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\Documents\Actions\CreateDocument;
use Liberu\CRM\Documents\Actions\CreateDocumentLink;
use Liberu\CRM\Documents\Actions\CreateDocumentVersion;
use Liberu\CRM\Documents\Actions\RecordDocumentEngagement;
use Liberu\CRM\Documents\Models\CrmDocument;
use Liberu\CRM\Documents\Queries\DocumentsQuery;

final class DocumentsController extends Controller
{
    public function __construct(private readonly DocumentsQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->documents($teamId)->get());
    }

    public function store(CreateDocument $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function version(CrmDocument $document, CreateDocumentVersion $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $document, request()->all()), 201);
    }

    public function link(CrmDocument $document, CreateDocumentLink $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $document, request('expires_at')), 201);
    }

    public function engagement(CrmDocument $document, RecordDocumentEngagement $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $document, request()->all()), 201);
    }
}
