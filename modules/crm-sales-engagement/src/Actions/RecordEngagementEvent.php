<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesEngagement\Models\EngagementEvent;
use Liberu\CRM\SalesEngagement\Models\EngagementSequence;
use Liberu\CRM\SalesEngagement\Models\Enrollment;
use Liberu\CRM\SalesEngagement\Services\EngagementPolicy;

final class RecordEngagementEvent
{
    public function execute(int $teamId, int $actorId, array $data): EngagementEvent
    {
        if (! app(EngagementPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        validator($data, ['contact_id' => ['required', 'integer'], 'event' => ['required', 'in:reply,meeting,email_open,email_click,call_completed'], 'payload' => ['nullable', 'array']])->validate();
        if (! DB::table('contacts')->where('team_id', $teamId)->where('id', $data['contact_id'])->exists()) {
            throw ValidationException::withMessages(['contact_id' => 'Contact does not belong to this team.']);
        }

        return DB::transaction(function () use ($teamId, $actorId, $data): EngagementEvent {
            $event = EngagementEvent::query()->create(['team_id' => $teamId, 'contact_id' => $data['contact_id'], 'event' => $data['event'], 'payload' => $data['payload'] ?? []]);

            if (! in_array($data['event'], ['reply', 'meeting'], true)) {
                return $event;
            }

            $enrollments = Enrollment::query()
                ->where('team_id', $teamId)
                ->where('contact_id', $data['contact_id'])
                ->where('status', 'active')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $sequences = EngagementSequence::query()
                ->where('team_id', $teamId)
                ->whereIn('id', $enrollments->pluck('sequence_id'))
                ->get()->keyBy('id');

            foreach ($enrollments as $enrollment) {
                $sequence = $sequences->get($enrollment->sequence_id);

                if ($sequence !== null && (bool) ($sequence->stop_rules[$data['event']] ?? true)) {
                    app(StopEnrollment::class)->execute($teamId, $actorId, $enrollment->id, $data['event']);
                }
            }

            return $event;
        });
    }
}
