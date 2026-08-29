<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\BusinessProcessManagement\Actions\AdvanceProcess;
use Liberu\CRM\BusinessProcessManagement\Actions\CreateProcess;
use Liberu\CRM\BusinessProcessManagement\Actions\PublishProcess;
use Liberu\CRM\BusinessProcessManagement\Actions\StartProcess;
use Tests\TestCase;

final class BusinessProcessManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_version_can_be_published_and_run_to_completion(): void
    {
        $process = app(CreateProcess::class)->execute(701, 12, [
            'key' => 'customer-review',
            'name' => 'Customer review',
            'version' => 2,
            'definition' => ['steps' => ['validate', ['key' => 'approve', 'approval' => true]]],
        ]);

        app(PublishProcess::class)->execute(701, 12, $process);
        $run = app(StartProcess::class)->execute(701, 12, $process->fresh(), ['case' => 'A-1']);
        $run = app(AdvanceProcess::class)->execute(701, 12, $run, ['validated' => true]);
        $run = app(AdvanceProcess::class)->execute(701, 12, $run, ['approved' => true]);

        $this->assertSame(2, $process->fresh()->version);
        $this->assertSame('completed', $run->status);
        $this->assertCount(3, $run->events()->get());
        $this->assertDatabaseMissing('crm_bpm_processes', ['team_id' => 702]);
    }
}
