<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\EmailMarketing\Actions\CreateEmailCampaign;
use Liberu\CRM\EmailMarketing\Actions\RecordMarketingEvent;
use Liberu\CRM\EmailMarketing\Actions\ScheduleCampaign;
use Liberu\CRM\EmailMarketing\Models\EmailCampaign;
use Liberu\CRM\EmailMarketing\Queries\EmailMarketingQuery;

final class EmailMarketingController extends Controller
{
    public function __construct(private readonly EmailMarketingQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->campaigns($teamId)->get());
    }

    public function store(CreateEmailCampaign $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function schedule(EmailCampaign $campaign, ScheduleCampaign $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $campaign, request()->all()));
    }

    public function event(EmailCampaign $campaign, RecordMarketingEvent $action): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($action->execute($teamId, $campaign, request()->all()), 201);
    }

    public function analytics(EmailCampaign $campaign): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->analytics($teamId, $campaign->id));
    }
}
