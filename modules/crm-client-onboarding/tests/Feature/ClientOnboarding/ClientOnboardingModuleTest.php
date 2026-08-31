<?php

declare(strict_types=1);

namespace Tests\Feature\ClientOnboarding;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ClientOnboarding\Actions\CompleteOnboardingStep;
use Liberu\CRM\ClientOnboarding\Actions\StartClientOnboarding;
use Tests\TestCase;

final class ClientOnboardingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_intake_checklist_health_and_handoff_are_scoped(): void
    {
        $u = User::factory()->create();
        $t = Team::factory()->create(['user_id' => $u->id]);
        $o = app(StartClientOnboarding::class)->execute($t->id, $u->id, 'client-1', ['domain' => 'example.test']);
        $step = app(CompleteOnboardingStep::class)->execute($t->id, $u->id, $o, 'verification', 'DNS verified', ['record' => 'ok']);
        $handoff = app(CompleteOnboardingStep::class)->execute($t->id, $u->id, $o->fresh(), 'handoff', 'Accepted', ['owner' => 'success']);
        $this->assertSame('completed', $step->status);
        $this->assertSame('complete', $o->fresh()->status);
        $this->assertSame(20, $o->fresh()->health);
    }
}
