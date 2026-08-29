<?php

declare(strict_types=1);

namespace Tests\Feature\MarketingDevelopmentFunds;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\MarketingDevelopmentFunds\Actions\CreateFund;
use Liberu\CRM\MarketingDevelopmentFunds\Actions\CreateMdfRequest;
use Liberu\CRM\MarketingDevelopmentFunds\Actions\RecordMdfEvent;
use Tests\TestCase;

final class MarketingDevelopmentFundsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_fund_request_reimbursement_and_roi_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $fund = app(CreateFund::class)->execute($team->id, $owner->id, ['name' => 'FY26', 'budget' => 10000, 'currency' => 'USD', 'starts_on' => now()->toDateString(), 'status' => 'active']);
        $request = app(CreateMdfRequest::class)->execute($team->id, $owner->id, $fund, ['title' => 'Partner campaign', 'partner_id' => 7, 'amount' => 1000]);
        app(RecordMdfEvent::class)->execute($team->id, $owner->id, $request, ['kind' => 'approval', 'status' => 'approved']);
        app(RecordMdfEvent::class)->execute($team->id, $owner->id, $request, ['kind' => 'reimbursement', 'status' => 'paid', 'amount' => 750]);
        app(RecordMdfEvent::class)->execute($team->id, $owner->id, $request, ['kind' => 'roi', 'status' => 'recorded', 'amount' => 5000]);
        $this->assertDatabaseHas('crm_mdf_requests', ['team_id' => $team->id, 'reimbursed' => '750.00', 'attributed_revenue' => '5000.00']);
        $this->assertDatabaseMissing('crm_mdf_funds', ['team_id' => $other->id, 'name' => 'FY26']);
    }
}
