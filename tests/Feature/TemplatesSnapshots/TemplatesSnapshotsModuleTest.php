<?php

declare(strict_types=1);

namespace Tests\Feature\TemplatesSnapshots;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\TemplatesAndSnapshots\Actions\CreateSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\InstallSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\RollbackSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\ShareSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\UpdateSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Models\SnapshotAudit;
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

    public function test_snapshot_mutations_reject_foreign_manager_and_control_fields(): void
    {
        $owner = User::factory()->create();
        $foreign = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);

        $this->expectException(ValidationException::class);
        app(CreateSnapshot::class)->execute($team->id, $foreign->id, [
            'name' => 'Foreign',
            'payload' => ['settings' => ['timezone' => 'UTC']],
            'team_id' => 999999,
            'created_by' => $owner->id,
        ]);
    }

    public function test_rollback_stays_within_the_current_team(): void
    {
        $owner = User::factory()->create();
        $otherOwner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create(['user_id' => $otherOwner->id]);
        $create = app(CreateSnapshot::class);
        $bundle = $create->execute($team->id, $owner->id, ['name' => 'Shared name', 'payload' => ['v' => 1], 'status' => 'published']);
        $create->execute($other->id, $otherOwner->id, ['name' => 'Shared name', 'payload' => ['v' => 1], 'status' => 'published']);
        app(InstallSnapshot::class)->execute($team->id, $owner->id, $bundle->id);

        self::assertSame(1, app(RollbackSnapshot::class)->execute($team->id, $owner->id, $bundle->id, 1)->getAttribute('version'));
    }

    public function test_draft_snapshots_can_be_updated_but_published_snapshots_are_immutable(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $draft = app(CreateSnapshot::class)->execute($team->id, $owner->id, ['name' => 'Initial', 'payload' => ['version' => 1]]);

        $updated = app(UpdateSnapshot::class)->execute($team->id, $owner->id, $draft->id, ['name' => 'Updated', 'payload' => ['version' => 2]]);

        self::assertSame('Updated', $updated->getAttribute('name'));
        self::assertSame(['version' => 2], $updated->getAttribute('payload'));
        self::assertSame('snapshot_updated', SnapshotAudit::query()->where('team_id', $team->id)->latest('id')->value('event'));

        $published = app(CreateSnapshot::class)->execute($team->id, $owner->id, ['name' => 'Published', 'payload' => ['version' => 1], 'status' => 'published']);
        $this->expectException(ValidationException::class);
        app(UpdateSnapshot::class)->execute($team->id, $owner->id, $published->id, ['payload' => ['version' => 2]]);
    }
}
