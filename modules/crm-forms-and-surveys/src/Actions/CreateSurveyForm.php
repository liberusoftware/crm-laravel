<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveys\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\FormsAndSurveys\Models\SurveyForm;
use Liberu\CRM\FormsAndSurveys\Services\FormPolicy;

final class CreateSurveyForm
{
    public function __construct(private readonly FormPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): SurveyForm
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['slug' => ['required', 'string', 'max:255'], 'kind' => ['required', 'in:form,survey'], 'status' => ['nullable', 'in:draft,published,archived'], 'schema' => ['required', 'array', 'min:1'], 'settings' => ['nullable', 'array'], 'embedding' => ['nullable', 'array']])->validate();

        return SurveyForm::query()->create(['team_id' => $teamId, ...$data]);
    }
}
