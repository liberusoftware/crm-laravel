<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Actions;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\TemplatesAndSnapshots\Models\SnapshotBundle;
use Liberu\CRM\TemplatesAndSnapshots\Services\SnapshotAudit;
use Liberu\CRM\TemplatesAndSnapshots\Services\SnapshotPolicy;

final class ShareSnapshot
{
    public function execute(int $teamId, int $actorId, int $bundleId): string
    {
        if (! app(SnapshotPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }$bundle = SnapshotBundle::query()->where('team_id', $teamId)->where('status', 'published')->findOrFail($bundleId);
        $token = Str::random(64);
        $bundle->update(['share_token_hash' => hash('sha256', $token), 'shared_at' => now()]);
        app(SnapshotAudit::class)->record($teamId, $actorId, 'snapshot_shared', ['bundle_id' => $bundleId]);

        return $token;
    }
}
