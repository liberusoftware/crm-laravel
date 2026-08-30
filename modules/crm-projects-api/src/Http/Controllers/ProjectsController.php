<?php

declare(strict_types=1);

namespace Liberu\CRM\ProjectsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\Projects\Actions\ChangeProjectStatus;
use Liberu\CRM\Projects\Actions\CreateProject;
use Liberu\CRM\Projects\Actions\CreateProjectTask;
use Liberu\CRM\Projects\Actions\HandoffOpportunity;
use Liberu\CRM\Projects\Actions\LogProjectTime;
use Liberu\CRM\Projects\Actions\RecordProjectRisk;
use Liberu\CRM\Projects\Queries\ProjectQuery;

final class ProjectsController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function projects(Request $r, ProjectQuery $q)
    {
        return response()->json(['data' => $q->projects($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function project(Request $r, CreateProject $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function task(Request $r, CreateProjectTask $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function time(Request $r, LogProjectTime $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function status(Request $r, int $project, string $status, ChangeProjectStatus $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $project, $status)]);
    }

    public function risk(Request $r, RecordProjectRisk $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function handoff(Request $r, int $project, HandoffOpportunity $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $project, (int) $r->input('opportunity_id'))]);
    }
}
