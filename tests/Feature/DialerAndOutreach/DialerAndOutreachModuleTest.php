<?php

declare(strict_types=1);

namespace Tests\Feature\DialerAndOutreach;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\DialerAndOutreach\Actions\CreateDialerList;
use Liberu\CRM\DialerAndOutreach\Actions\QueueDialerCall;
use Liberu\CRM\DialerAndOutreach\Actions\RecordCallOutcome;
use Liberu\CRM\DialerAndOutreach\Actions\RetryDialerCall;
use Tests\TestCase;

final class DialerAndOutreachModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_compliant_call_queue_outcome_and_retry_are_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $list = app(CreateDialerList::class)->execute($team->id, $owner->id, ['name' => 'Priority', 'mode' => 'progressive', 'compliance' => ['consent_required' => true], 'script' => ['opening' => 'Hello']]);
        $call = app(QueueDialerCall::class)->execute($team->id, $owner->id, $list, ['phone' => '+15551234567', 'consent' => true]);
        $event = app(RecordCallOutcome::class)->execute($team->id, $owner->id, $call, ['outcome' => 'voicemail', 'detection' => 'machine', 'voicemail_dropped' => true]);
        $retry = app(RetryDialerCall::class)->execute($team->id, $call);
        $this->assertSame('voicemail', $event->event);
        $this->assertSame('queued', $retry->status);
        $this->assertTrue((bool) $call->fresh()->voicemail_dropped);
    }
}
