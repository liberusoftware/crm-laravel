<?php

declare(strict_types=1);

namespace Tests\Feature\RevenueIntelligence;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\RevenueIntelligence\Actions\CreateAlert;
use Liberu\CRM\RevenueIntelligence\Actions\RecordInsight;
use Liberu\CRM\RevenueIntelligence\Actions\ResolveAlert;
use Liberu\CRM\RevenueIntelligence\Filament\Resources\InsightResource;
use Liberu\CRM\RevenueIntelligence\Models\RevenueInsight;
use Liberu\CRM\RevenueIntelligence\Models\RevenueIntelligenceAlert;
use Tests\TestCase;

final class RevenueIntelligenceModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_insight_resource_exposes_the_complete_filament_lifecycle(): void
    {
        self::assertSame(['index', 'create', 'edit'], array_keys(InsightResource::getPages()));
    }

    public function test_insights_and_alerts_are_team_scoped_and_recoverable(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $insight = app(RecordInsight::class)->execute($team->id, $owner->id, ['subject_type' => 'opportunity', 'subject_id' => 7, 'kind' => 'score', 'score' => 88, 'severity' => 'info', 'payload' => ['reason' => 'engagement']]);
        $alert = app(CreateAlert::class)->execute($team->id, $owner->id, ['kind' => 'anomaly', 'severity' => 'warning', 'message' => 'Engagement dropped']);
        app(ResolveAlert::class)->execute($team->id, $owner->id, $alert->id);

        self::assertSame(88, $insight->score);
        self::assertSame('resolved', $alert->refresh()->status);
        self::assertCount(0, RevenueInsight::query()->where('team_id', $other->id)->get());
        self::assertCount(0, RevenueIntelligenceAlert::query()->where('team_id', $other->id)->get());
    }
}
