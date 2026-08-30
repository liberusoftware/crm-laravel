<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\LeadQualification\Events\QualificationChanged;
use Liberu\CRM\LeadQualification\Models\LeadQualification;
use Liberu\CRM\LeadQualification\Models\NurtureEnrollment;
use Liberu\CRM\LeadQualification\Services\QualificationAudit;

final class EnrollNurture
{
    /** @param array<string, mixed> $attributes */
    public function execute(LeadQualification $qualification, ?int $actorId, array $attributes): NurtureEnrollment
    {
        if (blank($attributes['sequence'] ?? null)) {
            throw ValidationException::withMessages(['sequence' => 'A nurture sequence is required.']);
        }
        if ($qualification->qualification_status === 'converted') {
            throw ValidationException::withMessages(['qualification' => 'A converted qualification cannot enter nurture.']);
        }

        return DB::transaction(function () use ($qualification, $actorId, $attributes): NurtureEnrollment {
            $startsAt = $attributes['starts_at'] ?? now();
            $nurture = $qualification->nurtures()->create(array_merge($attributes, ['team_id' => $qualification->team_id, 'actor_id' => $actorId, 'status' => 'active', 'starts_at' => $startsAt]));
            $qualification->update(['qualification_status' => 'nurturing', 'nurture_until' => $attributes['ends_at'] ?? null, 'version' => $qualification->version + 1]);
            app(QualificationAudit::class)->record($qualification, $actorId, 'qualification.nurture_enrolled', ['sequence' => $attributes['sequence']]);
            QualificationChanged::dispatch($qualification, 'nurture_enrolled');

            return $nurture->refresh();
        });
    }
}
