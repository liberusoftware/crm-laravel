<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFunds\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\MarketingDevelopmentFunds\Models\MdfRequest;
use Liberu\CRM\MarketingDevelopmentFunds\Models\MdfRequestEvent;
use Liberu\CRM\MarketingDevelopmentFunds\Services\MdfPolicy;

final class RecordMdfEvent
{
    public function __construct(private readonly MdfPolicy $policy) {}

    public function execute(int $teamId, int $userId, MdfRequest $request, array $input): MdfRequestEvent
    {
        abort_unless($request->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:approval,evidence,claim,reimbursement,roi'], 'status' => ['required', 'in:pending,approved,rejected,paid,recorded'], 'amount' => ['nullable', 'numeric', 'min:0'], 'evidence' => ['nullable', 'string'], 'metadata' => ['nullable', 'array']])->validate();
        $event = MdfRequestEvent::query()->create(['team_id' => $teamId, 'request_id' => $request->id, 'actor_id' => $userId, ...$data]);
        if ($event->kind === 'reimbursement' && $event->status === 'paid') {
            $request->increment('reimbursed', (float) ($event->amount ?? 0));
        }if ($event->kind === 'roi' && $event->status === 'recorded') {
            $request->increment('attributed_revenue', (float) ($event->amount ?? 0));
        }

        return $event;
    }
}
