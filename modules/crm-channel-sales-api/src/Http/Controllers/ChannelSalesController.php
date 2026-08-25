<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSalesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\ChannelSales\Actions\AdvanceChannelOpportunity;
use Liberu\CRM\ChannelSales\Actions\RegisterChannelOpportunity;
use Liberu\CRM\ChannelSales\Models\ChannelOpportunity;
use Liberu\CRM\ChannelSales\Queries\ChannelSalesQuery;

final class ChannelSalesController extends Controller
{
    public function __construct(private readonly ChannelSalesQuery $query) {}

    private function context(): array
    {
        $u = request()->user();

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function index(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->opportunities($t)->get());
    }

    public function store(RegisterChannelOpportunity $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, (string) request('partner_key'), (string) request('opportunity_key'), (float) request('amount'), (float) request('commission_rate'), (array) request('pricing_reference', [])), 201);
    }

    public function advance(ChannelOpportunity $opportunity, AdvanceChannelOpportunity $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, $opportunity, (string) request('stage'), (string) request('handoff_status', 'pending')));
    }

    public function forecast(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->forecast($t));
    }
}
