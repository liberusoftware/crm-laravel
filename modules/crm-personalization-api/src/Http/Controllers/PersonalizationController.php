<?php

declare(strict_types=1);

namespace Liberu\CRM\PersonalizationApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\Personalization\Actions\CreatePersonalizationRule;
use Liberu\CRM\Personalization\Actions\DecidePersonalization;
use Liberu\CRM\Personalization\Actions\RecordPersonalizationOutcome;
use Liberu\CRM\Personalization\Queries\PersonalizationQuery;

final class PersonalizationController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function rules(Request $r, PersonalizationQuery $q)
    {
        return response()->json(['data' => $q->rules($this->team($r))->get()]);
    }

    public function rule(Request $r, CreatePersonalizationRule $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function decision(Request $r, DecidePersonalization $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function outcome(Request $r, RecordPersonalizationOutcome $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
