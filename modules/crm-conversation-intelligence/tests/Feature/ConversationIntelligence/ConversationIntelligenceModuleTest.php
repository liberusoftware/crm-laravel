<?php

declare(strict_types=1);

namespace Tests\Feature\ConversationIntelligence;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ConversationIntelligence\Actions\AddEvidence;
use Liberu\CRM\ConversationIntelligence\Actions\AnalyzeConversation;
use Liberu\CRM\ConversationIntelligence\Actions\RecordConversation;
use Liberu\CRM\ConversationIntelligence\Queries\ConversationQuery;
use Tests\TestCase;

final class ConversationIntelligenceModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_analysis_evidence_and_search_are_team_scoped(): void
    {
        $u = User::factory()->create();
        $t = Team::factory()->create(['user_id' => $u->id]);
        $c = app(RecordConversation::class)->execute($t->id, $u->id, ['subject' => 'Discovery', 'type' => 'call']);
        $c = app(AnalyzeConversation::class)->execute($t->id, $c, 'We discussed pricing and a competitor.');
        $e = app(AddEvidence::class)->execute($t->id, $c, 'competitor', 'Rival', 'Competitor mentioned');
        $found = app(ConversationQuery::class)->evidence($t->id, 'Rival');
        $this->assertSame('analyzed', $c->status);
        $this->assertSame('competitor', $e->kind);
        $this->assertCount(1, $found);
    }
}
