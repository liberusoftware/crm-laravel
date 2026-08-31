<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\TemplatesAndSnapshots\Models\SnapshotBundle;
use Liberu\CRM\TemplatesAndSnapshots\Services\SnapshotAudit;
use Liberu\CRM\TemplatesAndSnapshots\Services\SnapshotPolicy;

final class UpdateSnapshot
{
    /** @param array{name?: string, payload?: array<string, mixed>, status?: string} $data */
    public function execute(int $teamId, int $actorId, int $bundleId, array $data): SnapshotBundle
    {
        if (! app(SnapshotPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        $data = validator($data, [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'payload' => ['sometimes', 'required', 'array'],
            'status' => ['sometimes', 'required', 'in:draft,published'],
        ])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $bundleId, $data): SnapshotBundle {
            $bundle = SnapshotBundle::query()->where('team_id', $teamId)->findOrFail($bundleId);

            if ($bundle->getAttribute('status') === 'published') {
                throw ValidationException::withMessages(['status' => 'Published snapshots are immutable; create a new version.']);
            }

            $payload = $data['payload'] ?? $bundle->getAttribute('payload');
            $bundle->update([
                ...$data,
                'payload' => $payload,
                'checksum' => hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR)),
            ]);
            app(SnapshotAudit::class)->record($teamId, $actorId, 'snapshot_updated', ['bundle_id' => $bundleId, 'version' => $bundle->getAttribute('version')]);

            return $bundle->fresh();
        });
    }
}
