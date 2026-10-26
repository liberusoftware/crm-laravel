<?php

declare(strict_types=1);

namespace Liberu\CRM\CampaignsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\Campaigns\Actions\CreateCampaign;
use Liberu\CRM\Campaigns\Actions\RecordCampaignEvent;
use Liberu\CRM\Campaigns\Models\Campaign;
use Liberu\CRM\Campaigns\Queries\CampaignQuery;

final class CampaignsController extends Controller
{
    public function __construct(private readonly CampaignQuery $query) {}

    private function context(): array
    {
        $u = request()->user();

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function index(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->campaigns($t)->get());
    }

    public function store(CreateCampaign $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, request()->all()), 201);
    }

    public function event(Campaign $campaign, RecordCampaignEvent $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, $campaign, (string) request('type'), (float) request('value', 0), (array) request('payload', [])), 201);
    }

    public function roi(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->roi($t));
    }
}
