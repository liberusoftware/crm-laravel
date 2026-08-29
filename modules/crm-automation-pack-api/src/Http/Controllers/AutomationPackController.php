<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPackApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\AutomationPack\Actions\ApproveAutomationRecipe;
use Liberu\CRM\AutomationPack\Actions\CreateAutomationRecipe;
use Liberu\CRM\AutomationPack\Actions\EnrollSubject;
use Liberu\CRM\AutomationPack\Actions\PublishAutomationRecipe;
use Liberu\CRM\AutomationPack\Actions\SimulateAutomationRecipe;
use Liberu\CRM\AutomationPack\Models\AutomationRecipe;
use Liberu\CRM\AutomationPack\Queries\AutomationPackQuery;

final class AutomationPackController extends Controller
{
    public function __construct(private readonly AutomationPackQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->recipes($teamId)->get());
    }

    public function store(CreateAutomationRecipe $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function publish(AutomationRecipe $recipe, PublishAutomationRecipe $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $recipe));
    }

    public function simulate(AutomationRecipe $recipe, SimulateAutomationRecipe $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $recipe, request()->all()), 201);
    }

    public function approve(AutomationRecipe $recipe, ApproveAutomationRecipe $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $recipe, (string) request('decision'), request('reason')));
    }

    public function enroll(AutomationRecipe $recipe, EnrollSubject $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $recipe, request()->all()), 201);
    }
}
