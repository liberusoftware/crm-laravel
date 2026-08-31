<?php

declare(strict_types=1);

namespace Tests\Feature\EmailMarketing;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\EmailMarketing\Actions\CreateEmailCampaign;
use Liberu\CRM\EmailMarketing\Actions\RecordMarketingEvent;
use Liberu\CRM\EmailMarketing\Actions\ScheduleCampaign;
use Tests\TestCase;

final class EmailMarketingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_campaign_content_scheduling_and_analytics_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $campaign = app(CreateEmailCampaign::class)->execute($team->id, $owner->id, ['name' => 'Launch', 'content_type' => 'code', 'subject' => 'Hello {{name}}', 'content' => '<p>{{dynamic}}</p>', 'personalization' => ['name' => 'contact.name'], 'dynamic_content' => ['dynamic' => 'variant'], 'deliverability' => ['unsubscribe_required' => true]]);
        $scheduled = app(ScheduleCampaign::class)->execute($team->id, $owner->id, $campaign, ['scheduled_at' => now()->addDay()->toDateTimeString()]);
        $event = app(RecordMarketingEvent::class)->execute($team->id, $scheduled, ['event' => 'opened']);
        $this->assertSame('scheduled', $scheduled->status);
        $this->assertSame($team->id, $event->team_id);
        $this->assertDatabaseHas('crm_email_marketing_campaigns', ['team_id' => $team->id, 'name' => 'Launch']);
    }
}
