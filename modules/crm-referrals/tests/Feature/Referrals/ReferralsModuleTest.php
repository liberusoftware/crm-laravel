<?php

declare(strict_types=1);

namespace Tests\Feature\Referrals;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Referrals\Actions\CreateProgram;
use Liberu\CRM\Referrals\Actions\CreateReferral;
use Liberu\CRM\Referrals\Actions\IssueReward;
use Liberu\CRM\Referrals\Actions\QualifyReferral;
use Liberu\CRM\Referrals\Filament\Resources\ReferralResource;
use Liberu\CRM\Referrals\Models\Referral;
use Liberu\CRM\Referrals\Models\ReferralReward;
use Tests\TestCase;

final class ReferralsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_referral_resource_exposes_the_complete_filament_lifecycle(): void
    {
        self::assertSame(['index', 'create', 'edit'], array_keys(ReferralResource::getPages()));
    }

    public function test_referral_attribution_qualification_and_idempotent_reward_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $program = app(CreateProgram::class)->execute($team->id, $owner->id, ['name' => 'Customer advocates', 'code_prefix' => 'ADV', 'reward_amount' => 50, 'reward_currency' => 'USD']);
        $referral = app(CreateReferral::class)->execute($team->id, $owner->id, ['program_id' => $program->id, 'advocate_id' => 12, 'prospect_email' => 'prospect@example.com', 'source' => 'email']);
        app(QualifyReferral::class)->execute($team->id, $owner->id, $referral->id, 'qualified');
        $reward = app(IssueReward::class)->execute($team->id, $owner->id, ['referral_id' => $referral->id, 'idempotency_key' => 'reward-1']);
        $sameReward = app(IssueReward::class)->execute($team->id, $owner->id, ['referral_id' => $referral->id, 'idempotency_key' => 'reward-1']);

        self::assertSame('qualified', $referral->refresh()->status);
        self::assertSame(50.0, $reward->amount);
        self::assertSame($reward->id, $sameReward->id);
        self::assertCount(0, Referral::query()->where('team_id', $other->id)->get());
        self::assertCount(1, ReferralReward::query()->where('team_id', $team->id)->get());
    }
}
