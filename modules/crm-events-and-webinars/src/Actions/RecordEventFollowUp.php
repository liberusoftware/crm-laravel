<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinars\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\EventsAndWebinars\Models\CrmEvent;
use Liberu\CRM\EventsAndWebinars\Models\EventFollowUp;
use Liberu\CRM\EventsAndWebinars\Services\EventPolicy;

final class RecordEventFollowUp
{
    public function __construct(private readonly EventPolicy $policy) {}

    public function execute(int $teamId, int $userId, CrmEvent $event, array $input): EventFollowUp
    {
        abort_unless($event->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:reminder,nurture,notification'], 'status' => ['nullable', 'in:queued,sent,failed'], 'payload' => ['required', 'array'], 'scheduled_at' => ['nullable', 'date']])->validate();

        return EventFollowUp::query()->create(['team_id' => $teamId, 'event_id' => $event->id, 'actor_id' => $userId, ...$data]);
    }
}
