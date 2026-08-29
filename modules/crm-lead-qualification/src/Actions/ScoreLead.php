<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\LeadQualification\Models\QualifiedLead;
use Liberu\CRM\LeadQualification\Services\LeadQualificationPolicy;

final class ScoreLead
{
    public function __construct(private readonly LeadQualificationPolicy $policy) {}

    public function execute(int $teamId, int $userId, QualifiedLead $lead, array $input): QualifiedLead
    {
        abort_unless($lead->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['fit_score' => ['required', 'integer', 'between:0,100'], 'engagement_score' => ['required', 'integer', 'between:0,100'], 'qualification' => ['nullable', 'in:unqualified,MQL,PQL,SQL,service_qualified']])->validate();
        $lead->fill($data)->save();

        return $lead->refresh();
    }
}
