<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\Routing\Models\RoutingAssignment;
use Liberu\CRM\Routing\Services\RoutingPolicy;

final class AcceptAssignment
{
    public function execute(int $teamId, int $actorId, int $id, string $status): RoutingAssignment
    {
        if (! app(RoutingPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }if (! in_array($status, ['accepted', 'rejected', 'expired'], true)) {
            throw ValidationException::withMessages(['status' => 'Invalid assignment status.']);
        }$assignment = RoutingAssignment::query()->where('team_id', $teamId)->findOrFail($id);
        $assignment->status = $status;
        if ($status === 'accepted') {
            $assignment->accepted_at = now();
        }$assignment->save();
        if (in_array($status, ['rejected', 'expired'], true)) {
            $assignment->agent()->decrement('workload');
        }

        return $assignment;
    }
}
