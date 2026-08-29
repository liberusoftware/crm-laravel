<?php

declare(strict_types=1);

namespace Tests\Feature\ProspectingAgent;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ProspectingAgent\Actions\ApproveAgentRun;
use Liberu\CRM\ProspectingAgent\Actions\CreateAgentRun;
use Liberu\CRM\ProspectingAgent\Actions\PrepareSequence;
use Liberu\CRM\ProspectingAgent\Actions\SelectTarget;
use Liberu\CRM\ProspectingAgent\Filament\Resources\AgentRunResource;
use Liberu\CRM\ProspectingAgent\Models\AgentRun;
use Tests\TestCase;

final class ProspectingAgentModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_run_resource_exposes_the_complete_filament_lifecycle(): void
    {
        self::assertSame(['index', 'create', 'edit'], array_keys(AgentRunResource::getPages()));
    }

    public function test_approval_required_agent_run_controls_target_and_sequence_workflow(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $run = app(CreateAgentRun::class)->execute($team->id, $owner->id, ['name' => 'Q3 outbound', 'targeting' => ['industry' => 'software'], 'policy' => ['requires_approval' => true]]);
        app(ApproveAgentRun::class)->execute($team->id, $owner->id, $run->id);
        $target = app(SelectTarget::class)->execute($team->id, $owner->id, ['run_id' => $run->id, 'prospect_id' => 42, 'research' => ['source' => 'approved']]);
        $sequence = app(PrepareSequence::class)->execute($team->id, $owner->id, ['run_id' => $run->id, 'target_id' => $target->id, 'step' => 1, 'channel' => 'email', 'content' => 'Hello {{first_name}}']);

        self::assertSame('approved', $run->refresh()->status);
        self::assertSame('prepared', $sequence->status);
        self::assertCount(0, AgentRun::query()->where('team_id', $other->id)->get());
    }
}
