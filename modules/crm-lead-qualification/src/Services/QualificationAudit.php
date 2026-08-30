<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Services;

use Liberu\CRM\LeadQualification\Models\LeadQualification;

final class QualificationAudit
{
    /** @param array<string, mixed> $details */
    public function record(LeadQualification $qualification, ?int $actorId, string $event, array $details = []): void
    {
        $qualification->getConnection()->table('crm_lead_qualification_audits')->insert([
            'team_id' => $qualification->team_id,
            'qualification_id' => $qualification->getKey(),
            'actor_id' => $actorId,
            'event' => $event,
            'details' => json_encode($details, JSON_THROW_ON_ERROR),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
