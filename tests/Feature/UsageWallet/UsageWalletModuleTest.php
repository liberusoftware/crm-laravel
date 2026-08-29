<?php

declare(strict_types=1);

namespace Tests\Feature\UsageWallet;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\UsageWalletAndRebilling\Actions\CreateCharge;
use Liberu\CRM\UsageWalletAndRebilling\Actions\ImportProviderUsage;
use Liberu\CRM\UsageWalletAndRebilling\Actions\UpsertWallet;
use Liberu\CRM\UsageWalletAndRebilling\Models\UsageCharge;
use Liberu\CRM\UsageWalletAndRebilling\Models\UsageImport;
use Liberu\CRM\UsageWalletAndRebilling\Models\UsageWallet;
use Tests\TestCase;

final class UsageWalletModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_usage_import_wallet_and_charge_are_team_scoped_and_duplicate_safe(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        app(UpsertWallet::class)->execute($team->id, $owner->id, ['currency' => 'USD', 'threshold' => 10, 'reload_amount' => 50]);
        $import = app(ImportProviderUsage::class)->execute($team->id, $owner->id, ['provider' => 'provider', 'external_id' => 'usage-1', 'amount' => 10, 'currency' => 'USD']);
        $same = app(ImportProviderUsage::class)->execute($team->id, $owner->id, ['provider' => 'provider', 'external_id' => 'usage-1', 'amount' => 99, 'currency' => 'USD']);
        self::assertSame($import->id, $same->id);
        $charge = app(CreateCharge::class)->execute($team->id, $owner->id, ['usage_import_id' => $import->id, 'markup_percent' => 25]);
        self::assertSame($charge->id, app(CreateCharge::class)->execute($team->id, $owner->id, ['usage_import_id' => $import->id, 'markup_percent' => 25])->id);
        self::assertSame(1, UsageWallet::query()->where('team_id', $team->id)->count());
        self::assertSame(0, UsageImport::query()->where('team_id', $other->id)->count());
        self::assertSame(1, UsageCharge::query()->where('team_id', $team->id)->count());
    }

    public function test_wallet_mutations_ignore_unvalidated_control_fields(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $wallet = app(UpsertWallet::class)->execute($team->id, $owner->id, ['currency' => 'USD', 'threshold' => 10, 'reload_amount' => 50, 'team_id' => 999, 'balance' => 900]);

        self::assertSame($team->id, $wallet->team_id);
        self::assertSame(0.0, (float) $wallet->balance);

        $import = app(ImportProviderUsage::class)->execute($team->id, $owner->id, ['provider' => 'provider', 'external_id' => 'usage-control', 'amount' => 10, 'currency' => 'USD', 'status' => 'failed']);
        self::assertSame('imported', $import->status);
    }
}
