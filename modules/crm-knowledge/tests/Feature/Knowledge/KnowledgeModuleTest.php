<?php

declare(strict_types=1);

namespace Tests\Feature\Knowledge;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Knowledge\Actions\CreateArticle;
use Liberu\CRM\Knowledge\Actions\RecordKnowledgeEvent;
use Tests\TestCase;

final class KnowledgeModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_article_approval_feedback_and_stale_controls_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $article = app(CreateArticle::class)->execute($team->id, $owner->id, ['slug' => 'reset', 'visibility' => 'public', 'locale' => 'en', 'title' => 'Reset password', 'body' => 'Steps']);
        app(RecordKnowledgeEvent::class)->execute($team->id, $owner->id, $article, ['kind' => 'approval', 'status' => 'approved']);
        app(RecordKnowledgeEvent::class)->execute($team->id, $owner->id, $article, ['kind' => 'feedback', 'status' => 'recorded', 'details' => 'Helpful']);
        app(RecordKnowledgeEvent::class)->execute($team->id, $owner->id, $article, ['kind' => 'stale_marked', 'status' => 'recorded']);
        $this->assertDatabaseHas('crm_knowledge_articles', ['team_id' => $team->id, 'status' => 'published']);
        $this->assertDatabaseHas('crm_knowledge_events', ['team_id' => $team->id, 'kind' => 'feedback']);
        $this->assertDatabaseMissing('crm_knowledge_articles', ['team_id' => $other->id, 'slug' => 'reset']);
    }
}
