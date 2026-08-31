<?php

declare(strict_types=1);

namespace Tests\Feature\Loyalty;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Loyalty\Actions\CreateProgram;
use Liberu\CRM\Loyalty\Actions\EnrollMember;
use Liberu\CRM\Loyalty\Actions\RecordPoints;
use Liberu\CRM\Loyalty\Actions\SetFraudControl;
use Tests\TestCase;

final class LoyaltyModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_points_ledger_fraud_control_and_statement_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $program = app(CreateProgram::class)->execute($team->id, $owner->id, ['name' => 'Rewards', 'currency' => 'PTS', 'status' => 'active']);
        $member = app(EnrollMember::class)->execute($team->id, $owner->id, $program, ['member_id' => $owner->id]);
        app(RecordPoints::class)->execute($team->id, $owner->id, $member, ['kind' => 'earn', 'points' => 100, 'reference' => 'earn-1']);
        app(RecordPoints::class)->execute($team->id, $owner->id, $member, ['kind' => 'redeem', 'points' => -25, 'reference' => 'redeem-1']);
        app(SetFraudControl::class)->execute($team->id, $owner->id, $member, ['status' => 'open', 'reason' => 'Review activity']);
        $this->assertDatabaseHas('crm_loyalty_members', ['team_id' => $team->id, 'balance' => '75.00', 'fraud_locked' => 1]);
        $this->assertDatabaseHas('crm_loyalty_ledger', ['team_id' => $team->id, 'kind' => 'redeem']);
        $this->assertDatabaseMissing('crm_loyalty_programs', ['team_id' => $other->id, 'name' => 'Rewards']);
    }
}
