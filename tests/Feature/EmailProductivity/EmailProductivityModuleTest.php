<?php

declare(strict_types=1);

namespace Tests\Feature\EmailProductivity;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\EmailProductivity\Actions\ConnectMailbox;
use Liberu\CRM\EmailProductivity\Actions\CreateEmailTemplate;
use Liberu\CRM\EmailProductivity\Actions\RecordEmailEvent;
use Liberu\CRM\EmailProductivity\Actions\SendEmail;
use Tests\TestCase;

final class EmailProductivityModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_mailbox_template_send_and_tracking_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $mailbox = app(ConnectMailbox::class)->execute($team->id, $owner->id, ['provider' => 'gmail', 'address' => 'team@example.com']);
        $template = app(CreateEmailTemplate::class)->execute($team->id, $owner->id, ['name' => 'Intro', 'subject' => 'Hello', 'body' => 'Welcome', 'shared' => true]);
        $message = app(SendEmail::class)->execute($team->id, $owner->id, ['mailbox_id' => $mailbox->id, 'to_address' => 'contact@example.com', 'subject' => $template->subject, 'body' => $template->body, 'tracking' => ['opens' => true]]);
        $event = app(RecordEmailEvent::class)->execute($team->id, $message, ['event' => 'opened']);
        $this->assertSame($team->id, $event->team_id);
        $this->assertSame('queued', $message->fresh()->status);
        $this->assertDatabaseHas('crm_email_messages', ['team_id' => $team->id, 'to_address' => 'contact@example.com']);
    }
}
