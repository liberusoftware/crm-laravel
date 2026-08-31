<?php

declare(strict_types=1);

namespace Tests\Feature\Segmentation;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Segmentation\Actions\CreateAudience;
use Liberu\CRM\Segmentation\Actions\RecordBehaviorEvent;
use Liberu\CRM\Segmentation\Actions\RefreshAudience;
use Liberu\CRM\Segmentation\Actions\UpdateAudience;
use Liberu\CRM\Segmentation\Models\Audience;
use Liberu\CRM\Segmentation\Models\AudienceMember;
use Liberu\CRM\Segmentation\Models\BehaviorEvent;
use Tests\TestCase;

final class SegmentationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_audience_conditions_events_exclusions_and_refresh_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $audience = app(CreateAudience::class)->execute($team->id, $owner->id, ['name' => 'Engaged', 'kind' => 'dynamic', 'conditions' => ['all' => [['field' => 'country', 'operator' => '=', 'value' => 'GB']]], 'exclusions' => [3], 'calculated_attributes' => ['tier' => 'engaged']]);
        app(UpdateAudience::class)->execute($team->id, $owner->id, $audience->id, ['status' => 'active']);
        app(RecordBehaviorEvent::class)->execute($team->id, $owner->id, ['contact_id' => 1, 'event' => 'ticket_resolved', 'properties' => ['channel' => 'email']]);
        app(RefreshAudience::class)->execute($team->id, $owner->id, $audience->id, ['contact_ids' => [1, 2, 3]]);

        self::assertSame(2, AudienceMember::query()->where('audience_id', $audience->id)->count());
        self::assertSame(1, BehaviorEvent::query()->where('team_id', $team->id)->count());
        self::assertSame(2, Audience::query()->whereKey($audience->id)->value('estimated_count'));
        self::assertSame(0, Audience::query()->where('team_id', $other->id)->count());
    }
}
