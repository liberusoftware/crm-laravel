<?php

declare(strict_types=1);

namespace Tests\Feature\WorkManagement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\WorkManagement\Actions\AddChecklistItem;
use Liberu\CRM\WorkManagement\Actions\AddDependency;
use Liberu\CRM\WorkManagement\Actions\CompleteWorkItem;
use Liberu\CRM\WorkManagement\Actions\CreateWorkItem;
use Liberu\CRM\WorkManagement\Actions\CreateWorkQueue;
use Liberu\CRM\WorkManagement\Actions\ReviewApproval;
use Liberu\CRM\WorkManagement\Actions\UpdateWorkItem;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkQueueResource;
use Liberu\CRM\WorkManagement\Models\WorkItem;
use Liberu\CRM\WorkManagement\Models\WorkQueue;
use Tests\TestCase;

final class WorkManagementModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_work_queue_has_domain_owned_lifecycle_and_filament_pages(): void
    {
        $queue = app(CreateWorkQueue::class)->execute(7, 11, ['name' => 'Review queue', 'rules' => ['priority' => 'high']]);
        $queue->update(['status' => 'paused']);

        self::assertSame(['index', 'create', 'edit'], array_keys(WorkQueueResource::getPages()));
        self::assertSame('paused', WorkQueue::query()->findOrFail($queue->id)->status);
        self::assertSame(1, $queue->getConnection()->table('crm_work_audits')->where('event', 'work_queue.created')->count());
    }

    public function test_work_item_lifecycle_checklist_approval_and_dependency_are_team_scoped(): void
    {
        $create = app(CreateWorkItem::class);
        $first = $create->execute(7, 11, ['title' => 'Prepare proposal', 'recurrence' => 'daily']);
        $second = $create->execute(7, 11, ['title' => 'Review proposal']);
        $otherTeam = $create->execute(8, 11, ['title' => 'Foreign item']);

        $check = app(AddChecklistItem::class)->execute($first, 11, 'Attach pricing');
        self::assertFalse($check->completed);
        $approval = app(ReviewApproval::class)->request($first, 11, 'Manager review');
        app(ReviewApproval::class)->review($approval, 12, 'approved');
        app(AddDependency::class)->execute($first, $second, 11);

        $updated = app(UpdateWorkItem::class)->execute($first, 11, ['status' => 'in_progress'], 1);
        $completed = app(CompleteWorkItem::class)->execute($updated, 11, 2);

        self::assertSame('completed', $completed->status);
        self::assertSame(3, WorkItem::query()->where('team_id', 7)->count());
        self::assertSame(1, WorkItem::query()->where('team_id', 8)->count());
        self::assertSame(2, WorkItem::query()->where('team_id', 7)->where('status', 'pending')->count());
        self::assertCount(7, $completed->getConnection()->table('crm_work_audits')->where('team_id', 7)->get());
        self::assertNotSame($otherTeam->team_id, $completed->team_id);
    }

    public function test_invalid_dependency_and_stale_updates_are_rejected(): void
    {
        $item = app(CreateWorkItem::class)->execute(7, 11, ['title' => 'One']);
        $other = app(CreateWorkItem::class)->execute(8, 11, ['title' => 'Two']);

        $this->expectException(ValidationException::class);
        app(AddDependency::class)->execute($item, $other, 11);
    }

    public function test_stale_update_is_rejected(): void
    {
        $item = app(CreateWorkItem::class)->execute(7, 11, ['title' => 'One']);

        $this->expectException(ValidationException::class);
        app(UpdateWorkItem::class)->execute($item, 11, ['status' => 'in_progress'], 99);
    }
}
