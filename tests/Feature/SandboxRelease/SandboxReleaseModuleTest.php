<?php

declare(strict_types=1);

namespace Tests\Feature\SandboxRelease;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\SandboxAndReleaseManagement\Actions\CreateChangeset;
use Liberu\CRM\SandboxAndReleaseManagement\Actions\CreateSnapshot;
use Liberu\CRM\SandboxAndReleaseManagement\Actions\PromoteChangeset;
use Liberu\CRM\SandboxAndReleaseManagement\Actions\RollbackChangeset;
use Liberu\CRM\SandboxAndReleaseManagement\Actions\ValidateChangeset;
use Liberu\CRM\SandboxAndReleaseManagement\Models\ReleaseDeployment;
use Liberu\CRM\SandboxAndReleaseManagement\Models\ReleaseSnapshot;
use Tests\TestCase;

final class SandboxReleaseModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_snapshot_validation_promotion_and_rollback_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $snapshot = app(CreateSnapshot::class)->execute($team->id, $owner->id, ['name' => 'Sandbox baseline', 'environment' => 'sandbox', 'configuration' => ['features' => ['a' => true]], 'test_data_policy' => ['mask' => true]]);
        $changeset = app(CreateChangeset::class)->execute($team->id, $owner->id, ['name' => 'Enable feature', 'changes' => ['features.a' => false], 'dependencies' => ['crm-core'], 'source_environment' => 'sandbox', 'target_environment' => 'staging']);
        app(ValidateChangeset::class)->execute($team->id, $owner->id, $changeset->id);
        $promotion = app(PromoteChangeset::class)->execute($team->id, $owner->id, $changeset->id);
        $rollback = app(RollbackChangeset::class)->execute($team->id, $owner->id, $changeset->id);

        self::assertNotNull($snapshot->checksum);
        self::assertSame(1, ReleaseSnapshot::query()->where('team_id', $team->id)->count());
        self::assertSame('succeeded', $promotion->status);
        self::assertSame('rollback', $rollback->operation);
        self::assertSame(2, ReleaseDeployment::query()->where('team_id', $team->id)->count());
    }
}
