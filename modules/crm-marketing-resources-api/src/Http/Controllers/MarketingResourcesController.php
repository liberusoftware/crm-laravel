<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResourcesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\MarketingResources\Actions\CreateMarketingResource;
use Liberu\CRM\MarketingResources\Actions\RecordResourceEvent;
use Liberu\CRM\MarketingResources\Models\MarketingResource;
use Liberu\CRM\MarketingResources\Queries\MarketingResourceQuery;

final class MarketingResourcesController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, MarketingResourceQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function store(Request $r, CreateMarketingResource $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function event(Request $r, MarketingResource $resource, RecordResourceEvent $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $resource, $r->all()), 201);
    }
}
