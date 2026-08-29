<?php

declare(strict_types=1);

namespace Tests\Feature\ConversationAnalytics;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ConversationAnalytics\Actions\RecordConversationAnalysis;
use Liberu\CRM\ConversationAnalytics\Actions\ScoreConversation;
use Liberu\CRM\ConversationAnalytics\Queries\ConversationAnalyticsQuery;
use Tests\TestCase;

final class ConversationAnalyticsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_scorecard_and_trends_are_team_scoped(): void
    {
        $u = User::factory()->create();
        $t = Team::factory()->create(['user_id' => $u->id]);
        $a = app(RecordConversationAnalysis::class)->execute($t->id, $u->id, 'conversation-1', ['observed_on' => '2026-08-25', 'topics' => ['pricing'], 'talk_ratios' => ['seller' => .6]]);
        $a = app(ScoreConversation::class)->execute($t->id, $a, ['discovery' => 80, 'clarity' => 100]);
        $trends = app(ConversationAnalyticsQuery::class)->trends($t->id);
        $this->assertEquals(90.0, $a->scorecard['average']);
        $this->assertSame(1, $trends['2026-08']['count']);
    }
}
