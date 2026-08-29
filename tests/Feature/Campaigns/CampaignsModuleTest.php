<?php

declare(strict_types=1);

namespace Tests\Feature\Campaigns;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Campaigns\Actions\CreateCampaign;
use Liberu\CRM\Campaigns\Actions\RecordCampaignEvent;
use Liberu\CRM\Campaigns\Queries\CampaignQuery;
use Tests\TestCase;

final class CampaignsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_campaign_hierarchy_budget_channels_events_and_roi_are_scoped(): void
    {
        $u = User::factory()->create();
        $t = Team::factory()->create(['user_id' => $u->id]);
        $c = app(CreateCampaign::class)->execute($t->id, $u->id, ['name' => 'Launch', 'objectives' => ['pipeline'], 'budget' => 5000, 'channels' => ['email', 'social'], 'audience' => ['segment' => 'prospects']]);
        app(RecordCampaignEvent::class)->execute($t->id, $u->id, $c, 'cost', 1000);
        app(RecordCampaignEvent::class)->execute($t->id, $u->id, $c, 'revenue', 3000);
        $roi = app(CampaignQuery::class)->roi($t->id);
        $this->assertSame(3000.0, $c->fresh()->revenue);
        $this->assertSame(2.0, $roi[(string) $c->id]['roi']);
    }
}
