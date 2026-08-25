<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModelsApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\PredictiveModels\Actions\DetectDrift;
use Liberu\CRM\PredictiveModels\Actions\RecordEvaluation;
use Liberu\CRM\PredictiveModels\Actions\RecordPrediction;
use Liberu\CRM\PredictiveModels\Actions\RegisterPredictiveModel;
use Liberu\CRM\PredictiveModels\Queries\PredictiveModelQuery;

final class PredictiveModelsController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function models(Request $r, PredictiveModelQuery $q)
    {
        return response()->json(['data' => $q->models($this->team($r))->get()]);
    }

    public function model(Request $r, RegisterPredictiveModel $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function prediction(Request $r, RecordPrediction $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function evaluation(Request $r, RecordEvaluation $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function drift(Request $r, DetectDrift $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
