<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SandboxAndReleaseManagement\Events\ReleasePromoted;
use Liberu\CRM\SandboxAndReleaseManagement\Models\ReleaseChangeset;
use Liberu\CRM\SandboxAndReleaseManagement\Models\ReleaseDeployment;
use Liberu\CRM\SandboxAndReleaseManagement\Services\ReleaseAudit;
use Liberu\CRM\SandboxAndReleaseManagement\Services\ReleasePolicy;

final class PromoteChangeset
{
    public function execute(int $teamId, int $actorId, int $id, array $data = []): ReleaseDeployment
    {
        if (! app(ReleasePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }$set = ReleaseChangeset::query()->where('team_id', $teamId)->findOrFail($id);
        if ($set->status !== 'validated') {
            throw ValidationException::withMessages(['changeset' => 'Changeset must pass validation before promotion.']);
        }$deployment = ReleaseDeployment::query()->create(['team_id' => $teamId, 'changeset_id' => $set->id, 'environment' => $data['environment'] ?? $set->target_environment, 'operation' => 'promote', 'status' => 'succeeded', 'actor_id' => $actorId, 'comparison' => $data['comparison'] ?? []]);
        $set->update(['status' => 'promoted']);
        app(ReleaseAudit::class)->record($teamId, $actorId, 'changeset_promoted', ['deployment_id' => $deployment->id]);
        ReleasePromoted::dispatch($deployment);

        return $deployment;
    }
}
