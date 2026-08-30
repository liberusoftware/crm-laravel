<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\TemplatesAndSnapshots\Models\SnapshotBundle;
use Liberu\CRM\TemplatesAndSnapshots\Models\SnapshotInstall;
use Liberu\CRM\TemplatesAndSnapshots\Services\SnapshotAudit;
use Liberu\CRM\TemplatesAndSnapshots\Services\SnapshotPolicy;

final class InstallSnapshot
{
    public function execute(int $teamId, int $actorId, int $bundleId): SnapshotInstall
    {
        if (! app(SnapshotPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        return DB::transaction(function () use ($teamId, $actorId, $bundleId) {
            $bundle = SnapshotBundle::query()->where('team_id', $teamId)->where('status', 'published')->findOrFail($bundleId);
            $install = SnapshotInstall::query()->updateOrCreate(['team_id' => $teamId, 'bundle_id' => $bundle->id], ['version' => $bundle->getAttribute('version'), 'status' => 'installed', 'installed_by' => $actorId]);
            app(SnapshotAudit::class)->record($teamId, $actorId, 'snapshot_installed', ['bundle_id' => $bundle->id, 'version' => $bundle->getAttribute('version')]);

            return $install;
        });
    }
}
