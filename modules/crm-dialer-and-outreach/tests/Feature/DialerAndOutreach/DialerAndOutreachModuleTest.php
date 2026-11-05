<?php

declare(strict_types=1);

namespace Tests\Feature\DialerAndOutreach;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Liberu\CRM\DialerAndOutreach\Actions\CreateDialerList;
use Liberu\CRM\DialerAndOutreach\Actions\QueueDialerCall;
use Liberu\CRM\DialerAndOutreach\Actions\RecordCallOutcome;
use Liberu\CRM\DialerAndOutreach\Actions\RetryDialerCall;
use Liberu\CRM\DialerAndOutreachLivewire\DialerAndOutreachLivewireServiceProvider;
use Liberu\CRM\DialerAndOutreachLivewire\Livewire\DialerDashboard;
use Liberu\CRM\Telephony\Models\TelephonySettings;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

    public function test_dispatch_places_one_call_and_blocks_a_second_until_completion(): void
    {
        Http::fake(['api.twilio.com/*' => Http::response(['sid' => 'CA'.str_repeat('a', 32)], 201)]);
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        TelephonySettings::query()->create([
            'team_id' => $team->id, 'account_sid' => 'AC'.str_repeat('a', 32), 'auth_token' => 'secret', 'default_from_number' => '+15551234567',
        ]);
        $list = app(CreateDialerList::class)->execute($team->id, $owner->id, [
            'name' => 'Sales', 'mode' => 'progressive', 'compliance' => ['consent_required' => true],
            'script' => ['opening' => 'Connecting you to sales.', 'agent_number' => '+15551234568'],
        ]);
        $list->update(['status' => 'active']);
        $action = app(QueueDialerCall::class);
        $call = $action->execute($team->id, $owner->id, $list, ['phone' => '+15551234569', 'consent' => true]);
        $action->execute($team->id, $owner->id, $list, ['phone' => '+15551234570', 'consent' => true]);
        $this->travelTo(Carbon::parse('2026-11-05 20:00:00', 'UTC'));
        $list->update(['local_time_policy' => ['timezone' => 'UTC', 'start' => '09:00', 'end' => '17:00', 'days' => [1, 2, 3, 4, 5]]]);
        $this->assertNull($action->dispatch($team->id, $owner->id, $list));
        Http::assertNothingSent();
        $list->update(['local_time_policy' => null]);
        $dispatched = $action->dispatch($team->id, $owner->id, $list);
        $this->assertSame($call->id, $dispatched->id);
        $this->assertSame('in_progress', $dispatched->status);
        $this->assertSame(1, $dispatched->attempts);
        $this->assertNull($action->dispatch($team->id, $owner->id, $list));
        Http::assertSentCount(1);
        $url = URL::signedRoute('crm.dialer.status', ['call' => $call->id, 'attempt' => 1]);
        $this->postJson($url, ['CallSid' => 'CA'.str_repeat('a', 32), 'CallStatus' => 'no-answer'])->assertOk();
        $this->assertSame('no_answer', $call->refresh()->outcome);
        $this->assertSame('completed', $call->status);
        app(RetryDialerCall::class)->execute($team->id, $call);
        $this->postJson($url, ['CallSid' => 'CA'.str_repeat('a', 32), 'CallStatus' => 'no-answer'])->assertOk();
        $this->assertSame('queued', $call->refresh()->status);
        $this->postJson(route('crm.dialer.status', ['call' => $call->id, 'attempt' => 1]), ['CallSid' => 'CA'.str_repeat('a', 32), 'CallStatus' => 'completed'])->assertForbidden();
        Http::fake(fn () => throw new ConnectionException('Provider timeout'));
        try {
            $action->dispatch($team->id, $owner->id, $list);
            $this->fail('Expected provider timeout.');
        } catch (ConnectionException) {
            $this->assertDatabaseHas('crm_dialer_calls', ['list_id' => $list->id, 'status' => 'dispatch_unknown']);
        }
        $this->assertNull($action->dispatch($team->id, $owner->id, $list));
    }

    public function test_opted_out_numbers_cannot_be_requeued(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $list = app(CreateDialerList::class)->execute($team->id, $owner->id, ['name' => 'Sales', 'mode' => 'preview', 'compliance' => ['consent_required' => false]]);
        $action = app(QueueDialerCall::class);
        $call = $action->execute($team->id, $owner->id, $list, ['phone' => '+15551234569']);
        app(RecordCallOutcome::class)->execute($team->id, $owner->id, $call, ['outcome' => 'do_not_call']);
        $this->expectException(HttpException::class);
        $action->execute($team->id, $owner->id, $list, ['phone' => '+15551234569']);
    }

    public function test_dashboard_owner_can_pause_an_active_list(): void
    {
        $this->app->register(DialerAndOutreachLivewireServiceProvider::class);
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $owner->forceFill(['current_team_id' => $team->id])->save();
        $list = app(CreateDialerList::class)->execute($team->id, $owner->id, ['name' => 'Sales', 'mode' => 'progressive', 'compliance' => ['consent_required' => true]]);
        $list->update(['status' => 'active']);
        Livewire::actingAs($owner)->test(DialerDashboard::class)
            ->call('setStatus', $list->id, 'paused')->assertSee('paused');
        $this->assertSame('paused', $list->refresh()->status);
    }
}
