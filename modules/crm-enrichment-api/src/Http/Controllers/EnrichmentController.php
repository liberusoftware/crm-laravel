<?php

declare(strict_types=1);

namespace Liberu\CRM\EnrichmentApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\Enrichment\Actions\RecordChange;
use Liberu\CRM\Enrichment\Actions\RecordProvenance;
use Liberu\CRM\Enrichment\Actions\UpsertEnrichment;
use Liberu\CRM\Enrichment\Actions\VerifyProfile;
use Liberu\CRM\Enrichment\Models\EnrichmentProfile;
use Liberu\CRM\Enrichment\Queries\EnrichmentQuery;

final class EnrichmentController extends Controller
{
    public function __construct(private readonly EnrichmentQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->profiles($teamId)->get());
    }

    public function store(UpsertEnrichment $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function provenance(EnrichmentProfile $profile, RecordProvenance $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $profile, request()->all()), 201);
    }

    public function change(EnrichmentProfile $profile, RecordChange $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $profile, request()->all()), 201);
    }

    public function verify(EnrichmentProfile $profile, VerifyProfile $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $profile, (bool) request('verified')), 200);
    }
}
