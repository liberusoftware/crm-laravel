<?php

declare(strict_types=1);

namespace Tests\Feature\MarketingAgent;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\MarketingAgent\Actions\CreateAgentRequest;
use Liberu\CRM\MarketingAgent\Actions\CreateExperiment;
use Liberu\CRM\MarketingAgent\Actions\RecordAgentCheck;
use Tests\TestCase;

final class MarketingAgentModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_governed_assistance_and_experiments_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $request = app(CreateAgentRequest::class)->execute($team->id, $owner->id, ['kind' => 'content', 'brief' => 'Create a consent-safe campaign variation', 'audience' => ['segment' => 'customers']]);
        app(RecordAgentCheck::class)->execute($team->id, $owner->id, $request, ['kind' => 'consent', 'status' => 'passed', 'details' => 'Consent verified']);
        app(CreateExperiment::class)->execute($team->id, $owner->id, $request, ['name' => 'Welcome copy', 'variants' => [['name' => 'A'], ['name' => 'B']]]);
        $this->assertDatabaseHas('crm_marketing_agent_checks', ['team_id' => $team->id, 'status' => 'passed']);
        $this->assertDatabaseHas('crm_marketing_agent_experiments', ['team_id' => $team->id, 'name' => 'Welcome copy']);
        $this->assertDatabaseMissing('crm_marketing_agent_requests', ['team_id' => $other->id, 'brief' => 'Create a consent-safe campaign variation']);
    }
}
