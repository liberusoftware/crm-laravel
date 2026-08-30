<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveysApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\FormsAndSurveys\Actions\CreateSurveyForm;
use Liberu\CRM\FormsAndSurveys\Actions\RecordFollowUp;
use Liberu\CRM\FormsAndSurveys\Actions\SubmitForm;
use Liberu\CRM\FormsAndSurveys\Models\FormSubmission;
use Liberu\CRM\FormsAndSurveys\Models\SurveyForm;
use Liberu\CRM\FormsAndSurveys\Queries\FormQuery;

final class FormsController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, FormQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function store(Request $r, CreateSurveyForm $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function submit(Request $r, SurveyForm $form, SubmitForm $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $form, $r->all()), 201);
    }

    public function followUp(Request $r, FormSubmission $submission, RecordFollowUp $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $submission, $r->all()), 201);
    }
}
