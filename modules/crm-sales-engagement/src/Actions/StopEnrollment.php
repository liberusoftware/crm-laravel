<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesEngagement\Models\EngagementTask;
use Liberu\CRM\SalesEngagement\Models\Enrollment;
use Liberu\CRM\SalesEngagement\Services\EngagementPolicy;

final class StopEnrollment
{
    public function execute(int $teamId, int $actorId, int $id, string $reason): Enrollment
    {
        if (! app(EngagementPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        if (! in_array($reason, ['reply', 'meeting', 'manual', 'unsubscribed'], true)) {
            throw ValidationException::withMessages(['reason' => 'Invalid stop reason.']);
        }

        return DB::transaction(function () use ($teamId, $id): Enrollment {
            $enrollment = Enrollment::query()->where('team_id', $teamId)->lockForUpdate()->findOrFail($id);
            $enrollment->status = 'stopped';
            $enrollment->next_run_at = null;
            $enrollment->save();

            EngagementTask::query()->where('team_id', $teamId)
                ->where('enrollment_id', $enrollment->id)
                ->where('status', 'queued')
                ->update(['status' => 'cancelled']);

            return $enrollment;
        });
    }
}
