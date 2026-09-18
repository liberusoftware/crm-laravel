<?php

declare(strict_types=1);

namespace Tests\Feature\SalesWorkspace;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesWorkspace\Actions\CreateWorkspaceItem;
use Liberu\CRM\SalesWorkspace\Actions\QuickUpdate;
use Liberu\CRM\SalesWorkspace\Models\WorkspaceItem;
use Liberu\CRM\SalesWorkspace\Queries\WorkspaceQuery;
use Tests\TestCase;

final class SalesWorkspaceModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_prioritized_feed_overdue_agenda_and_quick_updates_are_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $item = app(CreateWorkspaceItem::class)->execute($team->id, $owner->id, ['kind' => 'deal', 'title' => 'Renewal', 'priority' => 'urgent', 'due_at' => now()->subDay(), 'next_action' => 'Call customer', 'risk_indicators' => ['stale' => true], 'customer_history' => ['last_contact' => 'email']]);
        app(QuickUpdate::class)->execute($team->id, $owner->id, $item->id, ['type' => 'status', 'payload' => ['status' => 'completed']]);

        self::assertSame(0, app(WorkspaceQuery::class)->overdue($team->id)->count());
        self::assertSame('completed', WorkspaceItem::query()->findOrFail($item->id)->status);
        self::assertSame('Call customer', $item->next_action);
    }

    public function test_item_owners_must_belong_to_the_team(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $outsider = User::factory()->create();

        $this->expectException(ValidationException::class);
        app(CreateWorkspaceItem::class)->execute($team->id, $owner->id, ['kind' => 'task', 'title' => 'Private task', 'owner_id' => $outsider->id]);
    }

    public function test_invalid_quick_update_states_are_rejected_without_mutation(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $item = app(CreateWorkspaceItem::class)->execute($team->id, $owner->id, ['kind' => 'task', 'title' => 'Follow up']);

        try {
            app(QuickUpdate::class)->execute($team->id, $owner->id, $item->id, ['type' => 'status', 'payload' => ['status' => 'invalid']]);
            self::fail('Invalid status should be rejected.');
        } catch (ValidationException) {
            self::assertSame('open', $item->fresh()->status);
        }

        $this->expectException(ValidationException::class);
        app(QuickUpdate::class)->execute($team->id, $owner->id, $item->id, ['type' => 'priority', 'payload' => ['priority' => 'critical']]);
    }
}
