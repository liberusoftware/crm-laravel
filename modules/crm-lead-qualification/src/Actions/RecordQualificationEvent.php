<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Actions;

use Illuminate\Support\Facades\DB;
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

        return DB::transaction(function () use ($teamId, $userId, $lead, $data): QualificationEvent {
            $event = QualificationEvent::query()->create(['team_id' => $teamId, 'lead_id' => $lead->id, 'actor_id' => $userId, ...$data]);

            match ($data['kind']) {
                'disqualification' => $lead->update(['stage' => 'disqualified', 'disqualification_reason' => $data['reason'] ?? null]),
                'nurture' => $lead->update(['stage' => 'nurture', 'nurture' => true]),
                'conversion' => $lead->update(['stage' => 'converted', 'conversion_reference' => $data['to_value'] ?? null]),
                default => null,
            };

            return $event->refresh();
        });
    }
}
