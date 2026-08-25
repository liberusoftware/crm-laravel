<?php

declare(strict_types=1);

namespace Tests\Feature\RevenueLifecycle;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\RevenueLifecycle\Actions\CreateOrder;
use Liberu\CRM\RevenueLifecycle\Actions\ManageAsset;
use Liberu\CRM\RevenueLifecycle\Actions\RecordUsageSignal;
use Liberu\CRM\RevenueLifecycle\Actions\ResolveFallout;
use Liberu\CRM\RevenueLifecycle\Models\RevenueAsset;
use Liberu\CRM\RevenueLifecycle\Models\RevenueFallout;
use Liberu\CRM\RevenueLifecycle\Models\RevenueOrder;
use Tests\TestCase;

final class RevenueLifecycleModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_order_usage_and_fallout_lifecycle_is_team_scoped(): void
    {
        $owner = User::factory()->create();
        $otherOwner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $otherTeam = Team::factory()->create(['user_id' => $otherOwner->id]);

        $asset = app(ManageAsset::class)->execute($team->id, $owner->id, ['customer_id' => 42, 'name' => 'Enterprise Plan', 'status' => 'active', 'lifecycle_action' => 'upgrade', 'renewal_date' => '2027-08-25', 'entitlements' => ['seats' => 50]]);
        app(RecordUsageSignal::class)->execute($team->id, $owner->id, $asset->id, ['feature' => 'api_calls', 'value' => 1200]);
        $order = app(CreateOrder::class)->execute($team->id, $owner->id, ['opportunity_id' => 99, 'value' => 12500, 'billing_reference' => 'BILL-99']);
        $fallout = RevenueFallout::query()->create(['team_id' => $team->id, 'subject_type' => RevenueOrder::class, 'subject_id' => $order->id, 'reason' => 'billing', 'status' => 'open', 'details' => ['retryable' => true]]);
        app(ResolveFallout::class)->execute($team->id, $owner->id, $fallout->id, ['status' => 'resolved']);

        self::assertSame('upgrade', $asset->refresh()->lifecycle_action);
        self::assertSame(1200, $asset->refresh()->usage_signals['api_calls']['value']);
        self::assertSame('pending', $order->status);
        self::assertSame('resolved', $fallout->refresh()->status);
        self::assertCount(0, RevenueAsset::query()->where('team_id', $otherTeam->id)->get());
    }
}
