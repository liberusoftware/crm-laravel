<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\LeadQualification\Events\QualificationChanged;
use Liberu\CRM\LeadQualification\Models\LeadQualification;
use Liberu\CRM\LeadQualification\Services\QualificationAudit;

final class TransitionLifecycle
{
    private const STAGES = ['subscriber', 'lead', 'marketing_qualified', 'product_qualified', 'sales_qualified', 'service_qualified', 'opportunity', 'customer'];

    public function execute(LeadQualification $qualification, ?int $actorId, string $stage, ?string $reason = null): LeadQualification
    {
        if (! in_array($stage, self::STAGES, true)) {
            throw ValidationException::withMessages(['lifecycle_stage' => 'Unsupported lifecycle stage.']);
        }
        if (in_array($qualification->qualification_status, ['converted', 'disqualified'], true)) {
            throw ValidationException::withMessages(['qualification' => 'A converted or disqualified qualification cannot transition.']);
        }
        if ($stage === 'customer' && $qualification->qualification_status !== 'converted') {
            throw ValidationException::withMessages(['lifecycle_stage' => 'Customer stage requires conversion.']);
        }

        return DB::transaction(function () use ($qualification, $actorId, $stage, $reason): LeadQualification {
            $from = $qualification->lifecycle_stage;
            if ($from !== $stage) {
                $qualification->update(['lifecycle_stage' => $stage, 'version' => $qualification->version + 1]);
                $qualification->stageHistory()->create(['team_id' => $qualification->team_id, 'actor_id' => $actorId, 'from_stage' => $from, 'to_stage' => $stage, 'reason' => $reason]);
                app(QualificationAudit::class)->record($qualification, $actorId, 'qualification.stage_changed', ['from' => $from, 'to' => $stage, 'reason' => $reason]);
                QualificationChanged::dispatch($qualification, 'stage_changed');
            }

            return $qualification->refresh();
        });
    }
}
