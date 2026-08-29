<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SandboxAndReleaseManagement\Models\ReleaseChangeset;
use Liberu\CRM\SandboxAndReleaseManagement\Services\ReleaseAudit;
use Liberu\CRM\SandboxAndReleaseManagement\Services\ReleasePolicy;

final class ValidateChangeset
{
    public function execute(int $teamId, int $actorId, int $id): ReleaseChangeset
    {
        if (! app(ReleasePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }$set = ReleaseChangeset::query()->where('team_id', $teamId)->findOrFail($id);
        $dependencies = $set->dependencies ?? [];
        $valid = collect($dependencies)->every(fn ($dependency) => is_string($dependency) && $dependency !== '');
        $set->validation = ['valid' => $valid, 'dependency_count' => count($dependencies), 'checked_at' => now()->toIso8601String()];
        $set->status = $valid ? 'validated' : 'failed';
        $set->save();
        app(ReleaseAudit::class)->record($teamId, $actorId, 'changeset_validated', ['changeset_id' => $id, 'valid' => $valid]);

        return $set;
    }
}
