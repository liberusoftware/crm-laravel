<?php

declare(strict_types=1);

namespace Tests\Feature\SaasPackaging;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\SaasPackaging\Actions\ChangeSubscriptionStatus;
use Liberu\CRM\SaasPackaging\Actions\ProvisionSubscription;
use Liberu\CRM\SaasPackaging\Actions\RecordUsage;
use Liberu\CRM\SaasPackaging\Models\SaasPlan;
use Liberu\CRM\SaasPackaging\Models\SaasSubscription;
use Liberu\CRM\SaasPackaging\Models\SaasUsage;
use Tests\TestCase;

final class SaasPackagingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_trial_provisioning_lifecycle_and_usage_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $plan = SaasPlan::query()->create(['key' => 'growth', 'name' => 'Growth', 'price' => 49, 'currency' => 'USD', 'trial_days' => 14, 'entitlements' => ['crm.core'], 'limits' => ['contacts' => 10000], 'active' => true]);
        $subscription = app(ProvisionSubscription::class)->execute($team->id, $owner->id, ['plan_id' => $plan->id, 'billing_provider' => 'stripe']);
        app(RecordUsage::class)->execute($team->id, $owner->id, ['feature' => 'contacts', 'quantity' => 10, 'period_start' => '2026-08-01', 'period_end' => '2026-09-01']);
        app(RecordUsage::class)->execute($team->id, $owner->id, ['feature' => 'contacts', 'quantity' => 5, 'period_start' => '2026-08-01', 'period_end' => '2026-09-01']);
        app(ChangeSubscriptionStatus::class)->execute($team->id, $owner->id, 'suspended');

        self::assertSame('trialing', $subscription->status);
        self::assertSame('suspended', SaasSubscription::query()->where('team_id', $team->id)->value('status'));
        self::assertSame(15, SaasUsage::query()->where('team_id', $team->id)->value('quantity'));
    }
}
