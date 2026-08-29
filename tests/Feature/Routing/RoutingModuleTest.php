<?php

declare(strict_types=1);

namespace Tests\Feature\Routing;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Routing\Actions\AcceptAssignment;
use Liberu\CRM\Routing\Actions\AssignSubject;
use Liberu\CRM\Routing\Actions\CreateRoutingRule;
use Liberu\CRM\Routing\Actions\UpsertRoutingAgent;
use Liberu\CRM\Routing\Models\RoutingAssignment;
use Tests\TestCase;

final class RoutingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_assignment_matches_capacity_and_acceptance_timer_lifecycle(): void
    {
        $owner = User::factory()->create();
        $agent = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $team->users()->attach($agent, ['role' => 'sales rep']);
        app(CreateRoutingRule::class)->execute($team->id, $owner->id, ['name' => 'UK sales', 'priority' => 1, 'conditions' => ['country' => 'GB'], 'action' => ['queue' => 'sales']]);
        app(UpsertRoutingAgent::class)->execute($team->id, $owner->id, ['user_id' => $agent->id, 'languages' => ['en'], 'skills' => ['enterprise'], 'sla_minutes' => 15]);
        $assignment = app(AssignSubject::class)->execute($team->id, $owner->id, ['subject_type' => 'lead', 'subject_id' => 42, 'skills' => ['enterprise'], 'language' => 'en', 'acceptance_minutes' => 10]);
        app(AcceptAssignment::class)->execute($team->id, $owner->id, $assignment->id, 'accepted');

        self::assertSame('accepted', RoutingAssignment::query()->findOrFail($assignment->id)->status);
        self::assertNotNull($assignment->acceptance_due_at);
    }

    public function test_assignment_requires_all_requested_skills_when_language_is_omitted(): void
    {
        $owner = User::factory()->create();
        $wrongAgent = User::factory()->create();
        $matchingAgent = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $team->users()->attach([$wrongAgent->id, $matchingAgent->id], ['role' => 'sales rep']);
        app(UpsertRoutingAgent::class)->execute($team->id, $owner->id, ['user_id' => $wrongAgent->id, 'skills' => ['basic'], 'sla_minutes' => 15]);
        app(UpsertRoutingAgent::class)->execute($team->id, $owner->id, ['user_id' => $matchingAgent->id, 'skills' => ['enterprise'], 'sla_minutes' => 15]);

        $assignment = app(AssignSubject::class)->execute($team->id, $owner->id, ['subject_type' => 'lead', 'subject_id' => 43, 'skills' => ['enterprise']]);

        self::assertSame($matchingAgent->id, $assignment->agent()->value('user_id'));
    }
}
