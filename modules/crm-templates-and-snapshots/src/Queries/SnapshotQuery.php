<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Queries;

use Liberu\CRM\TemplatesAndSnapshots\Models\SnapshotBundle;

final class SnapshotQuery
{
    public function list(int $teamId)
    {
        return SnapshotBundle::query()->where('team_id', $teamId)->latest()->paginate(25);
    }

    public function find(int $teamId, int $id): SnapshotBundle
    {
        return SnapshotBundle::query()->where('team_id', $teamId)->findOrFail($id);
    }

    public function preview(int $teamId, int $id): array
    {
        return $this->find($teamId, $id)->getAttribute('payload');
    }
}
