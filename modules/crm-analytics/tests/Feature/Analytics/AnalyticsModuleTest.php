<?php

declare(strict_types=1);

namespace Tests\Feature\Analytics;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Analytics\Actions\CreateAnalyticsAsset;
use Liberu\CRM\Analytics\Actions\ExecuteAnalyticsAsset;
use Tests\TestCase;

final class AnalyticsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_assets_execute_with_tenant_boundary(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $asset = app(CreateAnalyticsAsset::class)->execute($team->id, $owner->id, ['name' => 'Pipeline', 'kind' => 'dashboard', 'definition' => ['metric' => 'value']]);
        $run = app(ExecuteAnalyticsAsset::class)->execute($team->id, $owner->id, $asset);
        $this->assertSame('dashboard', $asset->kind);
        $this->assertSame('completed', $run->status);
        $this->assertSame($team->id, $run->team_id);
    }
}
