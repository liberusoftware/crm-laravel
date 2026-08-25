<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\CPQ\Actions\PriceQuote;
use Liberu\CRM\CPQ\Actions\SubmitQuote;
use Liberu\CRM\CPQ\Models\CpqQuote;

final class CpqController extends Controller
{
    private function context(): array
    {
        $u = request()->user();

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function store(PriceQuote $action): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($action->execute($t, $u, request()->all()), 201);
    }

    public function submit(CpqQuote $quote, SubmitQuote $action): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($action->execute($t, $u, $quote));
    }
}
