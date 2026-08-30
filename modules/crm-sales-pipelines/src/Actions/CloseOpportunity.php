<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesPipelines\Models\Opportunity;
use Liberu\CRM\SalesPipelines\Services\PipelineAudit;
use Liberu\CRM\SalesPipelines\Services\PipelinePolicy;

final class CloseOpportunity
{
    public function execute(int $teamId, int $actorId, int $id, string $status, ?string $reason = null): Opportunity
    {
        if (! app(PipelinePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }if (! in_array($status, ['won', 'lost'], true)) {
            throw ValidationException::withMessages(['status' => 'Invalid close status.']);
        }if ($status === 'lost' && ($reason === null || $reason === '')) {
            throw ValidationException::withMessages(['loss_reason' => 'A loss reason is required.']);
        }$opp = Opportunity::query()->where('team_id', $teamId)->findOrFail($id);
        $opp->status = $status;
        $opp->loss_reason = $reason;
        $opp->save();
        app(PipelineAudit::class)->record($teamId, $actorId, 'opportunity_closed', ['opportunity_id' => $opp->id, 'status' => $status]);

        return $opp;
    }
}
