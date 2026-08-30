<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspace\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Models\PaymentEvent;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Models\PaymentTransaction;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Services\OrdersPaymentsPolicy;

final class RecordPaymentEvent
{
    public function __construct(private readonly OrdersPaymentsPolicy $policy) {}

    public function execute(int $teamId, int $userId, PaymentTransaction $transaction, array $input): PaymentEvent
    {
        abort_unless($transaction->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:invoice_paid,refund,dispute,handoff'], 'status' => ['required', 'in:pending,completed,failed,recorded'], 'amount' => ['nullable', 'numeric', 'min:0'], 'notes' => ['nullable', 'string'], 'external_reference' => ['nullable', 'string'], 'payload' => ['nullable', 'array']])->validate();

        return DB::transaction(function () use ($transaction, $teamId, $data): PaymentEvent {
            $event = PaymentEvent::query()->create(['team_id' => $teamId, 'transaction_id' => $transaction->id, ...$data]);
            if ($event->kind === 'invoice_paid' && $event->status === 'completed') {
                $transaction->increment('paid_amount', (float) ($event->amount ?? 0));
            } if ($event->kind === 'refund' && $event->status === 'completed') {
                $transaction->increment('refunded_amount', (float) ($event->amount ?? 0));
            }

            return $event;
        });
    }
}
