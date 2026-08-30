<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\LeadQualification\Events\QualificationChanged;
use Liberu\CRM\LeadQualification\Models\LeadQualification;
use Liberu\CRM\LeadQualification\Services\QualificationAudit;

final class DisqualifyQualification
{
    public function execute(LeadQualification $qualification, ?int $actorId, string $reason): LeadQualification
    {
        if (blank($reason)) {
            throw ValidationException::withMessages(['reason' => 'A disqualification reason is required.']);
        }
        if ($qualification->qualification_status === 'converted') {
            throw ValidationException::withMessages(['qualification' => 'A converted qualification cannot be disqualified.']);
        }

        return DB::transaction(function () use ($qualification, $actorId, $reason): LeadQualification {
            $qualification->update(['qualification_status' => 'disqualified', 'disqualification_reason' => $reason, 'version' => $qualification->version + 1]);
            app(QualificationAudit::class)->record($qualification, $actorId, 'qualification.disqualified', ['reason' => $reason]);
            QualificationChanged::dispatch($qualification, 'disqualified');

            return $qualification->refresh();
        });
    }
}
