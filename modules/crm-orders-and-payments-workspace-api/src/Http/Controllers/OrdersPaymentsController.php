<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspaceApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Actions\CreateTransaction;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Actions\RecordPaymentEvent;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Models\PaymentTransaction;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Queries\TransactionQuery;

final class OrdersPaymentsController
{
    public function index(Request $request, TransactionQuery $query): JsonResponse
    {
        return response()->json($query->forTeam((int) $request->user()->current_team_id)->paginate());
    }

    public function store(Request $request, CreateTransaction $action): JsonResponse
    {
        return response()->json($action->execute((int) $request->user()->current_team_id, (int) $request->user()->id, $request->all()), 201);
    }

    public function event(Request $request, PaymentTransaction $transaction, RecordPaymentEvent $action): JsonResponse
    {
        return response()->json($action->execute((int) $request->user()->current_team_id, (int) $request->user()->id, $transaction, $request->all()), 201);
    }
}
