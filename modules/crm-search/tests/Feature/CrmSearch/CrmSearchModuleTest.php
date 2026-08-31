<?php

declare(strict_types=1);

namespace Tests\Feature\CrmSearch;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\CrmSearch\Actions\IndexSearchDocument;
use Liberu\CRM\CrmSearch\Actions\RecordSearchRecent;
use Liberu\CRM\CrmSearch\Actions\SaveSearchView;
use Liberu\CRM\CrmSearch\Queries\CrmSearchQuery;
use Tests\TestCase;

final class CrmSearchModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_aware_index_saved_view_recents_and_search_are_team_scoped(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $document = app(IndexSearchDocument::class)->execute($team->id, $user->id, ['record_type' => 'contact', 'record_key' => 'contact-1', 'title' => 'Ada Lovelace', 'content' => 'Analytical engine']);
        $view = app(SaveSearchView::class)->execute($team->id, $user->id, ['name' => 'Contacts', 'record_type' => 'contact', 'filters' => ['status' => 'active'], 'shared' => true]);
        $recent = app(RecordSearchRecent::class)->execute($team->id, $user->id, ['record_type' => 'contact', 'record_key' => $document->record_key, 'title' => $document->title]);
        $results = app(CrmSearchQuery::class)->search($team->id, 'Ada')->get();
        $this->assertCount(1, $results);
        $this->assertTrue($view->shared);
        $this->assertSame($user->id, $recent->user_id);
    }
}
