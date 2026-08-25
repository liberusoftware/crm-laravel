<?php

declare(strict_types=1);

namespace Tests\Feature\TemplatesSnapshots;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\TemplatesAndSnapshots\Actions\CreateSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\InstallSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\RollbackSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\ShareSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Models\SnapshotBundle;
use Liberu\CRM\TemplatesAndSnapshots\Models\SnapshotInstall;
use Tests\TestCase;

final class TemplatesSnapshotsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_versioned_snapshot_install_share_and_rollback_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $create = app(CreateSnapshot::class);
        $first = $create->execute($team->id, $owner->id, ['name' => 'Sales setup', 'payload' => ['pipelines' => ['default']], 'status' => 'published']);
        $second = $create->execute($team->id, $owner->id, ['name' => 'Sales setup', 'payload' => ['pipelines' => ['default', 'renewals']], 'status' => 'published']);
        $install = app(InstallSnapshot::class)->execute($team->id, $owner->id, $second->id);
        $token = app(ShareSnapshot::class)->execute($team->id, $owner->id, $second->id);
        $rolled = app(RollbackSnapshot::class)->execute($team->id, $owner->id, $second->id, 1);
        self::assertSame(1, $first->getAttribute('version'));
        self::assertSame(2, $second->getAttribute('version'));
        self::assertNotSame('', $token);
        self::assertSame(1, $rolled->getAttribute('version'));
        self::assertSame($install->getAttribute('id'), SnapshotInstall::query()->where('team_id', $team->id)->firstOrFail()->getAttribute('id'));
        self::assertSame(2, SnapshotBundle::query()->where('team_id', $team->id)->count());
        self::assertSame(0, SnapshotBundle::query()->where('team_id', $other->id)->count());
    }
}
