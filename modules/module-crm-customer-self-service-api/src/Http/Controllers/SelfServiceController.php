<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfServiceApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\CustomerSelfService\Actions\SubmitSelfServiceCase;
use Liberu\CRM\CustomerSelfService\Actions\UpdateSelfServicePreferences;
use Liberu\CRM\CustomerSelfService\Actions\UpsertSelfServiceProfile;
use Liberu\CRM\CustomerSelfService\Models\SelfServiceProfile;
use Liberu\CRM\CustomerSelfService\Queries\SelfServiceQuery;

final class SelfServiceController extends Controller
{
    public function __construct(private readonly SelfServiceQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    private function profileOrFail(int $teamId, int $userId): SelfServiceProfile
    {
        return $this->query->profile($teamId, $userId) ?? abort(404);
    }

    public function profile(): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($this->profileOrFail($teamId, $userId));
    }

    public function storeProfile(UpsertSelfServiceProfile $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()));
    }

    public function preferences(UpdateSelfServicePreferences $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $this->profileOrFail($teamId, $userId), request()->all()));
    }

    public function cases(): JsonResponse
    {
        [$teamId,$userId] = $this->context();
        $profile = $this->profileOrFail($teamId, $userId);

        return response()->json($this->query->cases($teamId, $profile->id)->get());
    }

    public function submitCase(SubmitSelfServiceCase $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $this->profileOrFail($teamId, $userId), request()->all()), 201);
    }

    public function knowledge(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->search($teamId, (string) request('q', ''))->get());
    }
}
