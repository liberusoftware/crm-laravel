<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveys\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\FormsAndSurveys\Models\FormFollowUp;
use Liberu\CRM\FormsAndSurveys\Models\FormSubmission;
use Liberu\CRM\FormsAndSurveys\Services\FormPolicy;

final class RecordFollowUp
{
    public function __construct(private readonly FormPolicy $policy) {}

    public function execute(int $teamId, int $userId, FormSubmission $submission, array $input): FormFollowUp
    {
        abort_unless($submission->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:notification,nurture,assignment'], 'status' => ['required', 'in:pending,queued,completed,failed'], 'details' => ['nullable', 'string'], 'scheduled_at' => ['nullable', 'date']])->validate();

        return FormFollowUp::query()->create(['team_id' => $teamId, 'submission_id' => $submission->id, ...$data]);
    }
}
