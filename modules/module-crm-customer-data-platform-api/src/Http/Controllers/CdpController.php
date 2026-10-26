<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatformApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\CustomerDataPlatform\Actions\ActivateCdpAudience;
use Liberu\CRM\CustomerDataPlatform\Actions\CreateCdpAudience;
use Liberu\CRM\CustomerDataPlatform\Actions\IngestCdpEvent;
use Liberu\CRM\CustomerDataPlatform\Actions\UpsertCdpProfile;
use Liberu\CRM\CustomerDataPlatform\Models\CdpAudience;
use Liberu\CRM\CustomerDataPlatform\Models\CdpProfile;
use Liberu\CRM\CustomerDataPlatform\Queries\CdpQuery;

final class CdpController extends Controller
{
    public function __construct(private readonly CdpQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function profiles(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->profiles($teamId)->get());
    }

    public function storeProfile(UpsertCdpProfile $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function event(CdpProfile $profile, IngestCdpEvent $action): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($action->execute($teamId, $profile, request()->all()), 201);
    }

    public function audiences(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->audiences($teamId)->get());
    }

    public function storeAudience(CreateCdpAudience $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function activate(CdpAudience $audience, ActivateCdpAudience $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $audience, (string) request('destination')));
    }
}
