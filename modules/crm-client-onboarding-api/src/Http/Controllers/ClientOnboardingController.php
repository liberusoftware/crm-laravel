<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboardingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\ClientOnboarding\Actions\CompleteOnboardingStep;
use Liberu\CRM\ClientOnboarding\Actions\StartClientOnboarding;
use Liberu\CRM\ClientOnboarding\Models\ClientOnboarding;
use Liberu\CRM\ClientOnboarding\Queries\ClientOnboardingQuery;

final class ClientOnboardingController extends Controller
{
    public function __construct(private readonly ClientOnboardingQuery $query) {}

    private function context(): array
    {
        $u = request()->user();

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function index(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->onboardings($t)->get());
    }

    public function store(StartClientOnboarding $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, (string) request('client_key'), (array) request('intake', [])), 201);
    }

    public function step(ClientOnboarding $onboarding, CompleteOnboardingStep $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, $onboarding, (string) request('kind'), (string) request('label'), (array) request('evidence', [])), 201);
    }
}
