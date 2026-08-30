<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnelsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\LandingPagesAndFunnels\Actions\AddFunnelPage;
use Liberu\CRM\LandingPagesAndFunnels\Actions\CreateFunnel;
use Liberu\CRM\LandingPagesAndFunnels\Actions\PublishFunnel;
use Liberu\CRM\LandingPagesAndFunnels\Actions\RecordFunnelEvent;
use Liberu\CRM\LandingPagesAndFunnels\Models\Funnel;
use Liberu\CRM\LandingPagesAndFunnels\Queries\FunnelQuery;

final class FunnelController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, FunnelQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function store(Request $r, CreateFunnel $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function page(Request $r, Funnel $funnel, AddFunnelPage $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $funnel, $r->all()), 201);
    }

    public function publish(Request $r, Funnel $funnel, PublishFunnel $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $funnel, $r->all()));
    }

    public function event(Request $r, Funnel $funnel, RecordFunnelEvent $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $funnel, $r->all()), 201);
    }
}
