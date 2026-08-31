<?php

declare(strict_types=1);

namespace Tests\Feature\MobileMessaging;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\MobileMessaging\Actions\CreateCampaign;
use Liberu\CRM\MobileMessaging\Actions\RecordMessage;
use Liberu\CRM\MobileMessaging\Actions\UpsertContactConsent;
use Tests\TestCase;

final class MobileMessagingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_consent_and_delivery_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $contact = app(UpsertContactConsent::class)->execute($team->id, $owner->id, ['address' => '+15550001', 'channel' => 'sms', 'consent' => 'opted_in', 'keyword' => 'START']);
        $campaign = app(CreateCampaign::class)->execute($team->id, $owner->id, ['name' => 'Welcome', 'channel' => 'sms']);
        app(RecordMessage::class)->execute($team->id, $owner->id, $campaign, ['contact_id' => $contact->id, 'direction' => 'outbound', 'status' => 'delivered', 'body' => 'Welcome']);
        $this->assertDatabaseHas('crm_mobile_messaging_contacts', ['team_id' => $team->id, 'consent' => 'opted_in']);
        $this->assertDatabaseHas('crm_mobile_messaging_messages', ['team_id' => $team->id, 'status' => 'delivered']);
        $this->assertDatabaseMissing('crm_mobile_messaging_contacts', ['team_id' => $other->id, 'address' => '+15550001']);
    }
}
