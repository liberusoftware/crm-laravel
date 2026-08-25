<?php

declare(strict_types=1);

namespace Tests\Feature\LandingPagesAndFunnels;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\LandingPagesAndFunnels\Actions\AddFunnelPage;
use Liberu\CRM\LandingPagesAndFunnels\Actions\CreateFunnel;
use Liberu\CRM\LandingPagesAndFunnels\Actions\PublishFunnel;
use Liberu\CRM\LandingPagesAndFunnels\Actions\RecordFunnelEvent;
use Tests\TestCase;

final class LandingPagesAndFunnelsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_funnel_publish_and_conversion_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $funnel = app(CreateFunnel::class)->execute($team->id, $owner->id, ['slug' => 'spring', 'name' => 'Spring Funnel']);
        $page = app(AddFunnelPage::class)->execute($team->id, $owner->id, $funnel, ['slug' => 'landing', 'kind' => 'landing', 'position' => 0, 'seo' => ['title' => 'Spring']]);
        app(PublishFunnel::class)->execute($team->id, $owner->id, $funnel, ['status' => 'published']);
        app(RecordFunnelEvent::class)->execute($team->id, $owner->id, $funnel, ['kind' => 'conversion', 'page_id' => $page->id, 'visitor_key' => 'v-1']);
        $this->assertDatabaseHas('crm_funnels', ['team_id' => $team->id, 'status' => 'published']);
        $this->assertDatabaseHas('crm_funnel_events', ['team_id' => $team->id, 'kind' => 'conversion']);
        $this->assertDatabaseMissing('crm_funnels', ['team_id' => $other->id, 'slug' => 'spring']);
    }
}
