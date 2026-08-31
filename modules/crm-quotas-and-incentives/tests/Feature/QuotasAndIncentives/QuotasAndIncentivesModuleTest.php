<?php

declare(strict_types=1);

namespace Tests\Feature\QuotasAndIncentives;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\QuotasAndIncentives\Actions\CreateCommissionPlan;
use Liberu\CRM\QuotasAndIncentives\Actions\CreateQuota;
use Liberu\CRM\QuotasAndIncentives\Actions\CreditCommission;
use Liberu\CRM\QuotasAndIncentives\Filament\Resources\QuotaResource;
use Liberu\CRM\QuotasAndIncentives\Models\CommissionCredit;
use Liberu\CRM\QuotasAndIncentives\Models\Quota;
use Tests\TestCase;

final class QuotasAndIncentivesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_quota_resource_exposes_the_complete_filament_lifecycle(): void
    {
        self::assertSame(['index', 'create', 'edit'], array_keys(QuotaResource::getPages()));
    }

    public function test_quota_and_idempotent_commission_credit_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $quota = app(CreateQuota::class)->execute($team->id, $owner->id, ['user_id' => $owner->id, 'period_start' => '2026-08-01', 'period_end' => '2026-08-31', 'target' => 100000, 'currency' => 'USD']);
        $plan = app(CreateCommissionPlan::class)->execute($team->id, $owner->id, ['name' => 'Standard', 'rate' => 0.1]);
        $credit = app(CreditCommission::class)->execute($team->id, $owner->id, ['user_id' => $owner->id, 'plan_id' => $plan->id, 'quota_id' => $quota->id, 'source_type' => 'order', 'source_id' => 55, 'amount' => 1000, 'idempotency_key' => 'credit-1']);
        $same = app(CreditCommission::class)->execute($team->id, $owner->id, ['user_id' => $owner->id, 'plan_id' => $plan->id, 'source_type' => 'order', 'source_id' => 55, 'amount' => 1000, 'idempotency_key' => 'credit-1']);

        self::assertSame(100000.0, $quota->target);
        self::assertSame(100.0, $credit->commission);
        self::assertSame($credit->id, $same->id);
        self::assertCount(0, Quota::query()->where('team_id', $other->id)->get());
        self::assertCount(1, CommissionCredit::query()->where('team_id', $team->id)->get());
    }
}
