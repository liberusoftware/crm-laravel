<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebillingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\UsageWalletAndRebilling\Actions\CreateCharge;
use Liberu\CRM\UsageWalletAndRebilling\Actions\ImportProviderUsage;
use Liberu\CRM\UsageWalletAndRebilling\Actions\UpsertWallet;
use Liberu\CRM\UsageWalletAndRebilling\Queries\UsageWalletQuery;

final class UsageWalletController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function summary(Request $r, UsageWalletQuery $q): JsonResponse
    {
        return response()->json(['data' => $q->summary($this->team($r))]);
    }

    public function imports(Request $r, UsageWalletQuery $q): JsonResponse
    {
        return response()->json($q->imports($this->team($r)));
    }

    public function charges(Request $r, UsageWalletQuery $q): JsonResponse
    {
        return response()->json($q->charges($this->team($r)));
    }

    public function import(Request $r, ImportProviderUsage $a): JsonResponse
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function charge(Request $r, CreateCharge $a): JsonResponse
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function wallet(Request $r, UpsertWallet $a): JsonResponse
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())]);
    }
}
