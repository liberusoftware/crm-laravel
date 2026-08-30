<?php

declare(strict_types=1);

namespace Tests\Feature\CustomerSuccess;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\CustomerSuccess\Actions\OpenSuccessRisk;
use Liberu\CRM\CustomerSuccess\Actions\PlanRenewal;
use Liberu\CRM\CustomerSuccess\Actions\RecordHealthSignal;
use Liberu\CRM\CustomerSuccess\Actions\UpsertSuccessCustomer;
use Tests\TestCase;

final class CustomerSuccessModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_health_risk_and_renewal_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $customer = app(UpsertSuccessCustomer::class)->execute($team->id, $owner->id, ['customer_key' => 'acme', 'segment' => 'enterprise', 'lifecycle' => 'adopted', 'health_score' => 82, 'success_plan' => ['outcome' => 'expansion']]);
        $signal = app(RecordHealthSignal::class)->execute($team->id, $owner->id, $customer, ['kind' => 'usage', 'value' => 95]);
        $risk = app(OpenSuccessRisk::class)->execute($team->id, $owner->id, $customer, ['kind' => 'adoption', 'severity' => 'medium', 'mitigation' => 'Enable training']);
        $renewal = app(PlanRenewal::class)->execute($team->id, $owner->id, $customer, ['renewal_date' => '2027-01-01', 'value' => 12000, 'attribution' => ['owner' => $owner->id]]);
        $this->assertSame($team->id, $signal->team_id);
        $this->assertSame('open', $risk->status);
        $this->assertSame($team->id, $renewal->team_id);
    }
}
