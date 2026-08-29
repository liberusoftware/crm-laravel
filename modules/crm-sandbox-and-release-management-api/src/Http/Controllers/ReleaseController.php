<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagementApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\SandboxAndReleaseManagement\Actions\CreateChangeset;
use Liberu\CRM\SandboxAndReleaseManagement\Actions\CreateSnapshot;
use Liberu\CRM\SandboxAndReleaseManagement\Actions\PromoteChangeset;
use Liberu\CRM\SandboxAndReleaseManagement\Actions\RollbackChangeset;
use Liberu\CRM\SandboxAndReleaseManagement\Actions\ValidateChangeset;
use Liberu\CRM\SandboxAndReleaseManagement\Queries\ReleaseQuery;

final class ReleaseController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function snapshots(Request $r, ReleaseQuery $q)
    {
        return response()->json(['data' => $q->snapshots($this->team($r))->get()]);
    }

    public function snapshot(Request $r, CreateSnapshot $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function changesets(Request $r, ReleaseQuery $q)
    {
        return response()->json(['data' => $q->changesets($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function changeset(Request $r, CreateChangeset $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function validateSet(Request $r, int $set, ValidateChangeset $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $set)]);
    }

    public function promote(Request $r, int $set, PromoteChangeset $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $set, $r->all())]);
    }

    public function rollback(Request $r, int $set, RollbackChangeset $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $set, $r->all())]);
    }
}
