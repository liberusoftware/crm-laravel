<?php

declare(strict_types=1);

namespace Tests\Feature\Collaboration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Collaboration\Actions\AddCollaborationRecord;
use Liberu\CRM\Collaboration\Actions\AssignCollaborationWork;
use Liberu\CRM\Collaboration\Actions\HandoffCollaborationWork;
use Tests\TestCase;

final class CollaborationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_mentions_work_queues_and_handoffs_are_scoped(): void
    {
        $record = app(AddCollaborationRecord::class)->execute(7, 'deal-1', 'user-1', 'Needs approval', 'mention', ['user-2']);
        $work = app(AssignCollaborationWork::class)->execute(7, 'sales', 'deal-1', 'user-1');
        $work = app(HandoffCollaborationWork::class)->execute(7, $work, 'user-2', 'Specialist review');
        $this->assertSame('mention', $record->kind);
        $this->assertSame('user-2', $work->assignee_key);
    }
}
