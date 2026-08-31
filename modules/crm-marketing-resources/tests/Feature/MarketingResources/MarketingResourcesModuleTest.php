<?php

declare(strict_types=1);

namespace Tests\Feature\MarketingResources;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\MarketingResources\Actions\CreateMarketingResource;
use Liberu\CRM\MarketingResources\Actions\RecordResourceEvent;
use Tests\TestCase;

final class MarketingResourcesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_resource_approval_and_rights_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $resource = app(CreateMarketingResource::class)->execute($team->id, $owner->id, ['key' => 'brand-logo', 'kind' => 'brand_kit', 'name' => 'Brand kit', 'file_reference' => 'files/logo.svg']);
        app(RecordResourceEvent::class)->execute($team->id, $owner->id, $resource, ['kind' => 'approval', 'status' => 'approved', 'notes' => 'Reviewed']);
        app(RecordResourceEvent::class)->execute($team->id, $owner->id, $resource, ['kind' => 'usage_right', 'status' => 'approved', 'expires_at' => now()->addYear()]);
        $this->assertDatabaseHas('crm_marketing_resources', ['team_id' => $team->id, 'status' => 'approved']);
        $this->assertDatabaseHas('crm_marketing_resource_events', ['team_id' => $team->id, 'kind' => 'usage_right']);
        $this->assertDatabaseMissing('crm_marketing_resources', ['team_id' => $other->id, 'key' => 'brand-logo']);
    }
}
