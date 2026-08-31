<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshotsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\TemplatesAndSnapshots\Actions\CreateSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\InstallSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\RollbackSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\ShareSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\UpdateSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Queries\SnapshotQuery;

final class SnapshotController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function index(Request $r, SnapshotQuery $q): JsonResponse
    {
        return response()->json($q->list($this->team($r)));
    }

    public function show(Request $r, int $snapshot, SnapshotQuery $q): JsonResponse
    {
        return response()->json(['data' => $q->find($this->team($r), $snapshot)]);
    }

    public function preview(Request $r, int $snapshot, SnapshotQuery $q): JsonResponse
    {
        return response()->json(['data' => $q->preview($this->team($r), $snapshot)]);
    }

    public function update(Request $r, int $snapshot, UpdateSnapshot $a): JsonResponse
    {
        $data = $r->validate(['name' => ['sometimes', 'required', 'string', 'max:255'], 'payload' => ['sometimes', 'required', 'array'], 'status' => ['sometimes', 'required', 'in:draft,published']]);

        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $snapshot, $data)]);
    }

    public function store(Request $r, CreateSnapshot $a): JsonResponse
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $r->validate(['name' => ['required', 'string', 'max:255'], 'payload' => ['required', 'array'], 'status' => ['sometimes', 'in:draft,published']]))], 201);
    }

    public function install(Request $r, int $snapshot, InstallSnapshot $a): JsonResponse
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $snapshot)]);
    }

    public function rollback(Request $r, int $snapshot, RollbackSnapshot $a): JsonResponse
    {
        $data = $r->validate(['version' => ['required', 'integer', 'min:1']]);

        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $snapshot, $data['version'])]);
    }

    public function share(Request $r, int $snapshot, ShareSnapshot $a): JsonResponse
    {
        return response()->json(['data' => ['token' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $snapshot)]]);
    }
}
