<?php

declare(strict_types=1);

namespace Tests\Feature\ServiceAnalytics;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ServiceAnalytics\Actions\RecordMetric;
use Liberu\CRM\ServiceAnalytics\Actions\RecordServiceMetrics;
use Liberu\CRM\ServiceAnalytics\Models\AnalyticsSnapshot;
use Liberu\CRM\ServiceAnalytics\Queries\AnalyticsQuery;
use Tests\TestCase;

final class ServiceAnalyticsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_metrics_are_idempotent_dimension_aware_and_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $period = ['period_start' => '2026-08-01 00:00:00', 'period_end' => '2026-09-01 00:00:00'];
        $metric = array_merge($period, ['metric' => 'volume', 'value' => 12, 'dimensions' => ['channel' => 'email']]);
        app(RecordMetric::class)->execute($team->id, $owner->id, $metric);
        app(RecordMetric::class)->execute($team->id, $owner->id, array_merge($metric, ['value' => 15]));
        app(RecordServiceMetrics::class)->execute($team->id, $owner->id, [array_merge($period, ['metric' => 'backlog', 'value' => 4]), array_merge($period, ['metric' => 'satisfaction', 'value' => 4.5])]);
        $summary = app(AnalyticsQuery::class)->summary($team->id);

        self::assertSame(3, AnalyticsSnapshot::query()->where('team_id', $team->id)->count());
        self::assertSame(15.0, $summary['volume']);
        self::assertSame(4.0, $summary['backlog']);
        self::assertSame(0, AnalyticsSnapshot::query()->where('team_id', $other->id)->count());
    }
}
