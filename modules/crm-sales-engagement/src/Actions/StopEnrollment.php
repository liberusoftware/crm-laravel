<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesEngagement\Models\Enrollment;
use Liberu\CRM\SalesEngagement\Services\EngagementPolicy;

final class StopEnrollment
{
    public function execute(int $teamId, int $actorId, int $id, string $reason): Enrollment
    {
        if (! app(EngagementPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }if (! in_array($reason, ['reply', 'meeting', 'manual', 'unsubscribed'], true)) {
            throw ValidationException::withMessages(['reason' => 'Invalid stop reason.']);
        }$enrollment = Enrollment::query()->where('team_id', $teamId)->findOrFail($id);
        $enrollment->status = 'stopped';
        $enrollment->save();

        return $enrollment;
    }
}
