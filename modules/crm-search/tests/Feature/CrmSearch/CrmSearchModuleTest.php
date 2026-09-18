<?php

declare(strict_types=1);

namespace Tests\Feature\CrmSearch;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\CrmSearch\Actions\IndexSearchDocument;
use Liberu\CRM\CrmSearch\Actions\RecordSearchRecent;
use Liberu\CRM\CrmSearch\Actions\SaveSearchView;
use Liberu\CRM\CrmSearch\Models\SearchRecent;
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
        app(IndexSearchDocument::class)->execute($team->id, $user->id, ['record_type' => 'contact', 'record_key' => 'contact-2', 'title' => 'Ada', 'content' => 'Short name']);
        $view = app(SaveSearchView::class)->execute($team->id, $user->id, ['name' => 'Contacts', 'record_type' => 'contact', 'filters' => ['status' => 'active'], 'shared' => true]);
        $recentAction = app(RecordSearchRecent::class);
        $recent = $recentAction->execute($team->id, $user->id, ['record_type' => 'contact', 'record_key' => $document->record_key, 'title' => $document->title]);
        $recentAction->execute($team->id, $user->id, ['record_type' => 'contact', 'record_key' => $document->record_key, 'title' => 'Ada Lovelace (updated)']);
        $results = app(CrmSearchQuery::class)->search($team->id, 'Ada')->get();
        $this->assertCount(2, $results);
        $this->assertSame('Ada', $results->first()->title);
        $this->assertSame(1, SearchRecent::query()->where('team_id', $team->id)->where('user_id', $user->id)->count());
        $this->assertCount(0, app(CrmSearchQuery::class)->search($team->id, '')->get());
        $this->assertTrue($view->shared);
        $this->assertSame($user->id, $recent->user_id);
    }
}
