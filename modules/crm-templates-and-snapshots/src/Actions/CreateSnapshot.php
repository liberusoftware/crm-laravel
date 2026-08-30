<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\TemplatesAndSnapshots\Models\SnapshotBundle;
use Liberu\CRM\TemplatesAndSnapshots\Services\SnapshotAudit;
use Liberu\CRM\TemplatesAndSnapshots\Services\SnapshotPolicy;

final class CreateSnapshot
{
    public function execute(int $teamId, int $actorId, array $data): SnapshotBundle
    {
        if (! app(SnapshotPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        $data = validator($data, ['name' => ['required', 'string', 'max:255'], 'payload' => ['required', 'array'], 'status' => ['sometimes', 'in:draft,published']])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $data): SnapshotBundle {
            $version = ((int) SnapshotBundle::query()->where('team_id', $teamId)->where('name', $data['name'])->max('version')) + 1;
            $payload = $data['payload'];
            $bundle = SnapshotBundle::query()->create(['team_id' => $teamId, 'name' => $data['name'], 'version' => $version, 'status' => $data['status'] ?? 'draft', 'payload' => $payload, 'checksum' => hash('sha256', (string) json_encode($payload, JSON_THROW_ON_ERROR)), 'created_by' => $actorId]);
            app(SnapshotAudit::class)->record($teamId, $actorId, 'snapshot_created', ['bundle_id' => $bundle->id, 'version' => $version]);

            return $bundle;
        });
    }
}
