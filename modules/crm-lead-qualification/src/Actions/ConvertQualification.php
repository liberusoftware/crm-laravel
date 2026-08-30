<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\LeadQualification\Events\QualificationConverted;
use Liberu\CRM\LeadQualification\Models\LeadQualification;
use Liberu\CRM\LeadQualification\Services\QualificationAudit;

final class ConvertQualification
{
    public function execute(LeadQualification $qualification, ?int $actorId, ?string $reason = null): LeadQualification
    {
        if ($qualification->qualification_status === 'converted') {
            return $qualification;
        }
        if ($qualification->qualification_status === 'disqualified') {
            throw ValidationException::withMessages(['qualification' => 'A disqualified qualification cannot convert.']);
        }

        return DB::transaction(function () use ($qualification, $actorId, $reason): LeadQualification {
            $fromStage = $qualification->lifecycle_stage;
            $qualification->update(['qualification_status' => 'converted', 'lifecycle_stage' => 'customer', 'converted_at' => now(), 'version' => $qualification->version + 1]);
            $qualification->stageHistory()->create(['team_id' => $qualification->team_id, 'actor_id' => $actorId, 'from_stage' => $fromStage, 'to_stage' => 'customer', 'reason' => $reason ?? 'converted']);
            app(QualificationAudit::class)->record($qualification, $actorId, 'qualification.converted', ['reason' => $reason]);
            QualificationConverted::dispatch($qualification);

            return $qualification->refresh();
        });
    }
}
