<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveys\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\FormsAndSurveys\Models\FormSubmission;
use Liberu\CRM\FormsAndSurveys\Models\SurveyForm;
use Liberu\CRM\FormsAndSurveys\Services\FormPolicy;

final class SubmitForm
{
    public function __construct(private readonly FormPolicy $policy) {}

    public function execute(int $teamId, int $userId, SurveyForm $form, array $input): FormSubmission
    {
        abort_unless($form->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['consent' => ['required', 'boolean'], 'visitor_key' => ['nullable', 'string', 'max:255'], 'attribution' => ['nullable', 'array'], 'payload' => ['required', 'array'], 'spam_status' => ['nullable', 'in:unchecked,passed,flagged']])->validate();
        abort_unless($form->status === 'published', 422);
        $submission = FormSubmission::query()->create(['team_id' => $teamId, 'form_id' => $form->id, ...$data]);
        $form->increment('submissions_count');

        return $submission;
    }
}
