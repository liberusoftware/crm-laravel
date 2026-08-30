<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\LeadQualification\Events\QualificationChanged;
use Liberu\CRM\LeadQualification\Models\LeadQualification;
use Liberu\CRM\LeadQualification\Services\QualificationAudit;

final class EvaluateQualification
{
    public function execute(LeadQualification $qualification, ?int $actorId): LeadQualification
    {
        return DB::transaction(function () use ($qualification, $actorId): LeadQualification {
            $framework = $qualification->framework;
            if ($framework === null) {
                throw ValidationException::withMessages(['framework' => 'A qualification framework is required.']);
            }
            if ($framework->team_id !== $qualification->team_id) {
                throw ValidationException::withMessages(['framework' => 'The framework belongs to another team.']);
            }
            $score = $qualification->total_score;
            $status = match (true) {
                $score >= $framework->service_qualified_threshold => 'service_qualified',
                $score >= $framework->sql_threshold => 'sales_qualified',
                $score >= $framework->pql_threshold => 'product_qualified',
                $score >= $framework->mql_threshold => 'marketing_qualified',
                default => 'unqualified',
            };
            $qualification->update(['qualification_status' => $status, 'version' => $qualification->version + 1]);
            app(QualificationAudit::class)->record($qualification, $actorId, 'qualification.evaluated', ['score' => $score, 'status' => $status, 'framework_id' => $framework->getKey()]);
            QualificationChanged::dispatch($qualification, 'evaluated');

            return $qualification->refresh();
        });
    }
}
