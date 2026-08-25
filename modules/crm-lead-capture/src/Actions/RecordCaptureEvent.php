<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\LeadCapture\Models\CapturedLead;
use Liberu\CRM\LeadCapture\Models\CaptureEvent;
use Liberu\CRM\LeadCapture\Services\LeadCapturePolicy;

final class RecordCaptureEvent
{
    public function __construct(private readonly LeadCapturePolicy $policy) {}

    public function execute(int $teamId, int $userId, CapturedLead $lead, array $input): CaptureEvent
    {
        abort_unless($lead->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:form_submitted,survey_completed,qr_scanned,chat_started,call_received,ad_clicked,event_attended,referral_received'], 'reference' => ['nullable', 'string', 'max:255'], 'payload' => ['nullable', 'array']])->validate();

        return CaptureEvent::query()->create(['team_id' => $teamId, 'lead_id' => $lead->id, ...$data]);
    }
}
