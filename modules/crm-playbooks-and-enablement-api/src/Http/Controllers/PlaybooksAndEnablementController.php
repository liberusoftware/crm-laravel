<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablementApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\PlaybooksAndEnablement\Actions\AssignPlaybook;
use Liberu\CRM\PlaybooksAndEnablement\Actions\CompletePlaybook;
use Liberu\CRM\PlaybooksAndEnablement\Actions\CreatePlaybook;
use Liberu\CRM\PlaybooksAndEnablement\Actions\RecommendPlaybook;
use Liberu\CRM\PlaybooksAndEnablement\Actions\RecordPlaybookUsage;
use Liberu\CRM\PlaybooksAndEnablement\Queries\PlaybookQuery;

final class PlaybooksAndEnablementController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function playbooks(Request $r, PlaybookQuery $q)
    {
        return response()->json(['data' => $q->playbooks($this->team($r))->get()]);
    }

    public function playbook(Request $r, CreatePlaybook $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function assignment(Request $r, AssignPlaybook $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function complete(Request $r, int $assignment, CompletePlaybook $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $assignment, $r->all())]);
    }

    public function recommendation(Request $r, RecommendPlaybook $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function usage(Request $r, RecordPlaybookUsage $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
