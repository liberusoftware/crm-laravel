<?php

declare(strict_types=1);

namespace Tests\Feature\ServiceAgent;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ServiceAgent\Actions\ClassifyCase;
use Liberu\CRM\ServiceAgent\Actions\CreateAgentCase;
use Liberu\CRM\ServiceAgent\Actions\EscalateAgentCase;
use Liberu\CRM\ServiceAgent\Actions\ReviewAgentCase;
use Liberu\CRM\ServiceAgent\Actions\RunAgentTool;
use Liberu\CRM\ServiceAgent\Actions\UpdateAgentOutput;
use Liberu\CRM\ServiceAgent\Models\AgentCase;
use Liberu\CRM\ServiceAgent\Models\AgentReview;
use Liberu\CRM\ServiceAgent\Models\AgentToolRun;
use Tests\TestCase;

final class ServiceAgentModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_case_is_idempotent_and_supports_full_operator_lifecycle(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $data = ['subject' => 'Cannot access account', 'input' => 'Customer cannot sign in.', 'idempotency_key' => 'agent-case-1'];
        $case = app(CreateAgentCase::class)->execute($team->id, $owner->id, $data);
        $same = app(CreateAgentCase::class)->execute($team->id, $owner->id, $data);
        app(ClassifyCase::class)->execute($team->id, $owner->id, $case->id, ['classification' => 'authentication', 'confidence' => 0.92]);
        app(UpdateAgentOutput::class)->execute($team->id, $owner->id, $case->id, 'draft', ['response_draft' => 'Please reset your password.']);
        app(UpdateAgentOutput::class)->execute($team->id, $owner->id, $case->id, 'plan', ['resolution_plan' => ['step' => 'verify identity']]);
        app(RunAgentTool::class)->execute($team->id, $owner->id, ['case_id' => $case->id, 'tool' => 'account_lookup', 'input' => ['email' => 'customer@example.test']]);
        app(EscalateAgentCase::class)->execute($team->id, $owner->id, $case->id, ['reason' => 'Customer requested an agent']);
        app(ReviewAgentCase::class)->execute($team->id, $owner->id, ['case_id' => $case->id, 'score' => 5, 'status' => 'approved']);

        self::assertSame($case->id, $same->id);
        self::assertSame(1, AgentCase::query()->where('team_id', $team->id)->count());
        self::assertSame('escalated', $case->fresh()->status);
        self::assertSame(1, AgentToolRun::query()->where('team_id', $team->id)->count());
        self::assertSame(1, AgentReview::query()->where('team_id', $team->id)->count());
    }
}
