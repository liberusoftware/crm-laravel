<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveys\Queries;

use Liberu\CRM\FormsAndSurveys\Models\SurveyForm;

final class FormQuery
{
    public function forTeam(int $teamId)
    {
        return SurveyForm::query()->where('team_id', $teamId)->with('submissions')->latest();
    }
}
