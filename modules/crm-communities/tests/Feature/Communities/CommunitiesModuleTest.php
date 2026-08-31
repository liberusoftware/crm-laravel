<?php

declare(strict_types=1);

namespace Tests\Feature\Communities;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Communities\Actions\CreateCommunitySpace;
use Liberu\CRM\Communities\Actions\JoinCommunity;
use Liberu\CRM\Communities\Actions\PublishCommunityContent;
use Liberu\CRM\Communities\Queries\CommunityQuery;
use Tests\TestCase;

final class CommunitiesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_membership_content_gamification_and_feed_are_scoped(): void
    {
        $u = User::factory()->create();
        $t = Team::factory()->create(['user_id' => $u->id]);
        $s = app(CreateCommunitySpace::class)->execute($t->id, $u->id, 'Customers');
        $m = app(JoinCommunity::class)->execute($t->id, $s, (string) $u->id);
        $content = app(PublishCommunityContent::class)->execute($t->id, $s, (string) $u->id, 'Welcome', 'post');
        $feed = app(CommunityQuery::class)->feed($t->id, $s->id)->get();
        $this->assertSame('published', $content->status);
        $this->assertSame(10, $m->fresh()->points);
        $this->assertCount(1, $feed);
    }
}
