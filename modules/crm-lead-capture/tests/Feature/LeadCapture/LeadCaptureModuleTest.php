<?php

declare(strict_types=1);

namespace Tests\Feature\LeadCapture;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\LeadCapture\Actions\CaptureLead;
use Liberu\CRM\LeadCapture\Actions\RecordCaptureEvent;
use Tests\TestCase;

final class LeadCaptureModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_multichannel_capture_and_source_events_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $lead = app(CaptureLead::class)->execute($team->id, $owner->id, ['external_key' => 'qr-1', 'channel' => 'qr', 'name' => 'Taylor', 'source' => 'spring-event', 'source_metadata' => ['campaign' => 'spring']]);
        app(RecordCaptureEvent::class)->execute($team->id, $owner->id, $lead, ['kind' => 'qr_scanned', 'reference' => 'code-1', 'payload' => ['location' => 'booth']]);
        $this->assertDatabaseHas('crm_lead_capture_leads', ['team_id' => $team->id, 'channel' => 'qr', 'source' => 'spring-event']);
        $this->assertDatabaseHas('crm_lead_capture_events', ['team_id' => $team->id, 'kind' => 'qr_scanned']);
        $this->assertDatabaseMissing('crm_lead_capture_leads', ['team_id' => $other->id, 'external_key' => 'qr-1']);
    }
}
