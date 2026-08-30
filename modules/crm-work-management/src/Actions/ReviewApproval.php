<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\WorkManagement\Models\Approval;
use Liberu\CRM\WorkManagement\Models\WorkItem;
use Liberu\CRM\WorkManagement\Services\WorkAudit;

final class ReviewApproval
{
    public function request(WorkItem $item, ?int $actorId, ?string $comment = null): Approval
    {
        return $item->approvals()->create(['team_id' => $item->team_id, 'requested_by' => $actorId, 'status' => 'pending', 'comment' => $comment]);
    }

    public function review(Approval $approval, int $actorId, string $status, ?string $comment = null): Approval
    {
        if (! in_array($status, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages(['status' => 'Approval status must be approved or rejected.']);
        }
        if ($approval->status !== 'pending') {
            throw ValidationException::withMessages(['status' => 'Only pending approvals can be reviewed.']);
        }

        return DB::transaction(function () use ($approval, $actorId, $status, $comment): Approval {
            $approval->update(['status' => $status, 'reviewed_by' => $actorId, 'reviewed_at' => now(), 'comment' => $comment ?? $approval->comment]);
            app(WorkAudit::class)->record($approval, $actorId, 'approval.reviewed', ['status' => $status]);

            return $approval->refresh();
        });
    }
}
