<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspaceApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\SalesWorkspace\Actions\CreateWorkspaceItem;
use Liberu\CRM\SalesWorkspace\Actions\QuickUpdate;
use Liberu\CRM\SalesWorkspace\Queries\WorkspaceQuery;

final class WorkspaceController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function feed(Request $r, WorkspaceQuery $q)
    {
        return response()->json(['data' => $q->feed($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function overdue(Request $r, WorkspaceQuery $q)
    {
        return response()->json(['data' => $q->overdue($this->team($r))->get()]);
    }

    public function agenda(Request $r, WorkspaceQuery $q)
    {
        return response()->json(['data' => $q->agenda($this->team($r))->get()]);
    }

    public function store(Request $r, CreateWorkspaceItem $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function update(Request $r, int $item, QuickUpdate $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $item, $r->all())]);
    }
}
