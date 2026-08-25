<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\LeadQualification\Models\QualificationEvent;
use Liberu\CRM\LeadQualification\Models\QualifiedLead;
use Liberu\CRM\LeadQualification\Services\LeadQualificationPolicy;

final class RecordQualificationEvent
{
    public function __construct(private readonly LeadQualificationPolicy $policy) {}

    public function execute(int $teamId, int $userId, QualifiedLead $lead, array $input): QualificationEvent
    {
        abort_unless($lead->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:stage_change,disqualification,nurture,conversion'], 'to_value' => ['nullable', 'string', 'max:255'], 'reason' => ['nullable', 'string'], 'payload' => ['nullable', 'array']])->validate();
        $event = QualificationEvent::query()->create(['team_id' => $teamId, 'lead_id' => $lead->id, 'actor_id' => $userId, ...$data]);
        if ($data['kind'] === 'disqualification') {
            $lead->update(['stage' => 'disqualified', 'disqualification_reason' => $data['reason'] ?? null]);
        }if ($data['kind'] === 'nurture') {
            $lead->update(['stage' => 'nurture', 'nurture' => true]);
        }if ($data['kind'] === 'conversion') {
            $lead->update(['stage' => 'converted', 'conversion_reference' => $data['to_value'] ?? null]);
        }

        return $event;
    }
}
