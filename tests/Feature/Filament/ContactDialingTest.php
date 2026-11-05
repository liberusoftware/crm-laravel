<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\App\Resources\ContactResource\Pages\ListContacts;
use App\Filament\App\Resources\LeadResource\Pages\ViewLead;
use App\Models\CallLog;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Liberu\CRM\DialerAndOutreach\Actions\QueueDialerCall;
use Liberu\CRM\DialerAndOutreach\Actions\StartContactDialing;
use Liberu\CRM\DialerAndOutreach\Models\DialerList;
use Liberu\CRM\DialerAndOutreach\Queries\ContactCallHistory;
use Liberu\CRM\Telephony\Models\TelephonySettings;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ContactDialingTest extends TestCase
{
    use RefreshDatabase;

    private function viewer(): User
    {
        $this->seed(RolesSeeder::class);
        $user = User::factory()->withPersonalTeam()->create(['email_verified_at' => now()]);
        setPermissionsTeamId($user->current_team_id);
        $user->assignRole('manager');
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($user->currentTeam);
        TelephonySettings::query()->create(['team_id' => $user->current_team_id, 'account_sid' => 'AC'.str_repeat('a', 32), 'auth_token' => 'test', 'default_from_number' => '+15551234567']);

        return $user;
    }

    private function input(int $days = 1): array
    {
        return ['agent_number' => '+15551234568', 'opening' => 'Connecting to sales.', 'cooldown_days' => $days];
    }

    private function callLog(Contact $contact, int $days): void
    {
        CallLog::query()->create(['team_id' => $contact->team_id, 'contact_id' => $contact->id, 'call_sid' => fake()->uuid(), 'direction' => 'outbound', 'status' => 'completed', 'created_at' => now()->subDays($days)]);
        CallLog::query()->where('contact_id', $contact->id)->update(['created_at' => now()->subDays($days)]);
    }

    public function test_bulk_queue_skips_recent_invalid_and_duplicate_contacts(): void
    {
        $user = $this->viewer();
        $contacts = Contact::factory()->count(3)->create(['team_id' => $user->current_team_id]);
        $contacts[0]->update(['phone_number' => '+15551234569']);
        $contacts[1]->update(['phone_number' => '+15551234570']);
        $contacts[2]->update(['phone_number' => 'invalid']);
        $this->callLog($contacts[1], 0);
        $action = app(StartContactDialing::class);
        $result = $action->execute($user, $contacts->modelKeys(), $this->input());
        $this->assertSame(1, $result['queued']);
        $this->assertSame(2, $result['skipped']);
        $this->assertSame('progressive', $result['list']->mode);
        $this->assertSame(0, $action->execute($user, [$contacts[0]->id], $this->input())['queued']);
    }

    public function test_history_filters_include_never_called_and_respect_day_windows(): void
    {
        $user = $this->viewer();
        $never = Contact::factory()->create(['team_id' => $user->current_team_id]);
        $older = Contact::factory()->create(['team_id' => $user->current_team_id]);
        $today = Contact::factory()->create(['team_id' => $user->current_team_id]);
        $this->callLog($older, 8);
        $this->callLog($today, 0);
        Livewire::test(ListContacts::class)->filterTable('last_called', '7')
            ->assertCanSeeTableRecords([$never, $older])->assertCanNotSeeTableRecords([$today])
            ->filterTable('last_called', '14')->assertCanSeeTableRecords([$never])->assertCanNotSeeTableRecords([$older, $today])
            ->filterTable('last_called', 'never')->assertCanSeeTableRecords([$never])->assertCanNotSeeTableRecords([$older, $today]);
        $this->assertNotNull(app(ContactCallHistory::class)->lastCalled($older));
    }

    public function test_dispatch_rechecks_cooldown_and_tracks_attempts_without_duplicate_calls(): void
    {
        $user = $this->viewer();
        $contact = Contact::factory()->create(['team_id' => $user->current_team_id, 'phone_number' => '+15551234569']);
        $result = app(StartContactDialing::class)->execute($user, [$contact->id], $this->input());
        Http::fake(['api.twilio.com/*' => Http::response(['sid' => 'CA'.str_repeat('b', 32)], 201)]);
        $call = app(QueueDialerCall::class)->dispatch((int) $user->current_team_id, (int) $user->id, $result['list']);
        $this->assertSame('in_progress', $call->status);
        $this->assertNotNull($call->last_attempt_at);
        $this->assertSame(0, app(StartContactDialing::class)->execute($user, [$contact->id], $this->input())['queued']);
        Http::assertSentCount(1);

        $another = Contact::factory()->create(['team_id' => $user->current_team_id, 'phone_number' => '+15551234570']);
        $queued = app(StartContactDialing::class)->execute($user, [$another->id], $this->input());
        $this->callLog($another, 0);
        $this->assertNull(app(QueueDialerCall::class)->dispatch((int) $user->current_team_id, (int) $user->id, $queued['list']));
        $this->assertDatabaseHas('crm_dialer_calls', ['contact_id' => $another->id, 'status' => 'blocked']);
    }

    public function test_lead_page_exposes_call_action_only_with_configured_voip(): void
    {
        $user = $this->viewer();
        $contact = Contact::factory()->create(['team_id' => $user->current_team_id, 'phone_number' => '+15551234569']);
        $lead = Lead::factory()->create(['team_id' => $user->current_team_id, 'user_id' => $user->id, 'contact_id' => $contact->id]);
        Livewire::test(ViewLead::class, ['record' => $lead->id])->assertActionVisible('makeCall');
        TelephonySettings::query()->where('team_id', $user->current_team_id)->delete();
        Livewire::test(ViewLead::class, ['record' => $lead->id])->assertActionHidden('makeCall');
    }

    public function test_lead_quick_call_and_bulk_pause_resume_actions(): void
    {
        $user = $this->viewer();
        Http::fake(['api.twilio.com/*' => Http::response(['sid' => 'CA'.str_repeat('c', 32)], 201)]);
        $contact = Contact::factory()->create(['team_id' => $user->current_team_id, 'phone_number' => '+15551234569']);
        $lead = Lead::factory()->create(['team_id' => $user->current_team_id, 'user_id' => $user->id, 'contact_id' => $contact->id]);
        Livewire::test(ViewLead::class, ['record' => $lead->id])
            ->callAction('makeCall', data: $this->input())->assertHasNoActionErrors();
        Http::assertSentCount(1);
        $this->assertDatabaseHas('crm_dialer_calls', ['contact_id' => $contact->id, 'status' => 'in_progress']);

        $another = Contact::factory()->create(['team_id' => $user->current_team_id, 'phone_number' => '+15551234570']);
        Livewire::test(ListContacts::class)->callTableBulkAction('bulkMakeCall', [$another], data: $this->input())
            ->assertHasNoTableBulkActionErrors();
        $list = DialerList::query()->where('mode', 'progressive')->latest('id')->firstOrFail();
        $this->assertNull(app(QueueDialerCall::class)->dispatch((int) $user->current_team_id, (int) $user->id, $list));
        Http::assertSentCount(1);
        Livewire::test(ListContacts::class)->callTableAction('manageDialing', data: ['list_id' => $list->id, 'status' => 'paused'])
            ->assertHasNoTableActionErrors();
        $this->assertSame('paused', $list->refresh()->status);
        Livewire::test(ListContacts::class)->callTableAction('manageDialing', data: ['list_id' => $list->id, 'status' => 'active'])
            ->assertHasNoTableActionErrors();
        $this->assertSame('active', $list->refresh()->status);
    }

    public function test_masked_user_cannot_start_calls_even_as_team_owner(): void
    {
        $user = $this->viewer();
        $user->syncRoles(['free']);
        $user->unsetRelation('roles');
        $contact = Contact::factory()->create(['team_id' => $user->current_team_id, 'phone_number' => '+15551234569']);
        $this->assertFalse(app(StartContactDialing::class)->available($user));
        $this->expectException(HttpException::class);
        app(StartContactDialing::class)->execute($user, [$contact->id], $this->input());
    }

    public function test_foreign_contact_is_denied_without_creating_a_list(): void
    {
        $user = $this->viewer();
        $other = User::factory()->withPersonalTeam()->create();
        $contact = Contact::factory()->create(['team_id' => $other->current_team_id]);
        $this->expectException(HttpException::class);
        app(StartContactDialing::class)->execute($user, [$contact->id], $this->input());
    }
}
