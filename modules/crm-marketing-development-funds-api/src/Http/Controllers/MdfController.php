<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFundsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\MarketingDevelopmentFunds\Actions\CreateFund;
use Liberu\CRM\MarketingDevelopmentFunds\Actions\CreateMdfRequest;
use Liberu\CRM\MarketingDevelopmentFunds\Actions\RecordMdfEvent;
use Liberu\CRM\MarketingDevelopmentFunds\Models\MdfFund;
use Liberu\CRM\MarketingDevelopmentFunds\Models\MdfRequest;
use Liberu\CRM\MarketingDevelopmentFunds\Queries\MdfQuery;

final class MdfController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, MdfQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function store(Request $r, CreateFund $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function request(Request $r, MdfFund $fund, CreateMdfRequest $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $fund, $r->all()), 201);
    }

    public function event(Request $r, MdfRequest $request, RecordMdfEvent $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $request, $r->all()), 201);
    }
}
