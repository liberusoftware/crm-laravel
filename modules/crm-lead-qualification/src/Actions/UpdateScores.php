<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\LeadQualification\Events\QualificationChanged;
use Liberu\CRM\LeadQualification\Models\LeadQualification;
use Liberu\CRM\LeadQualification\Services\QualificationAudit;
use Liberu\CRM\LeadQualification\Services\QualificationScorer;

final class UpdateScores
{
    /** @param array<string, mixed> $scores */
    public function execute(LeadQualification $qualification, ?int $actorId, array $scores): LeadQualification
    {
        return DB::transaction(function () use ($qualification, $actorId, $scores): LeadQualification {
            $values = app(QualificationScorer::class)->scores($scores);
            $qualification->update(array_merge($values, ['version' => $qualification->version + 1]));
            app(QualificationAudit::class)->record($qualification, $actorId, 'qualification.scored', $values);
            QualificationChanged::dispatch($qualification, 'scored');

            return $qualification->refresh();
        });
    }
}
