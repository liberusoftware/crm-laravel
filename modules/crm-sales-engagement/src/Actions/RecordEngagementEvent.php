<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesEngagement\Models\EngagementEvent;
use Liberu\CRM\SalesEngagement\Services\EngagementPolicy;

final class RecordEngagementEvent
{
    public function execute(int $teamId, int $actorId, array $data): EngagementEvent
    {
        if (! app(EngagementPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['contact_id' => ['required', 'integer'], 'event' => ['required', 'in:reply,meeting,email_open,email_click,call_completed'], 'payload' => ['nullable', 'array']])->validate();
        if (! DB::table('contacts')->where('team_id', $teamId)->where('id', $data['contact_id'])->exists()) {
            throw ValidationException::withMessages(['contact_id' => 'Contact does not belong to this team.']);
        }

        return EngagementEvent::query()->create(['team_id' => $teamId, 'contact_id' => $data['contact_id'], 'event' => $data['event'], 'payload' => $data['payload'] ?? []]);
    }
}
