<?php

declare(strict_types=1);

namespace Tests\Feature\Personalization;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Personalization\Actions\CreatePersonalizationRule;
use Liberu\CRM\Personalization\Actions\DecidePersonalization;
use Liberu\CRM\Personalization\Actions\RecordPersonalizationOutcome;
use Liberu\CRM\Personalization\Models\PersonalizationDecision;
use Liberu\CRM\Personalization\Models\PersonalizationOutcome;
use Tests\TestCase;

final class PersonalizationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_consent_aware_holdout_decision_and_outcome_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $rule = app(CreatePersonalizationRule::class)->execute($team->id, $owner->id, ['name' => 'Welcome offer', 'kind' => 'offer', 'conditions' => ['lifecycle' => 'new'], 'variants' => ['A' => ['offer' => 'welcome']], 'fallback' => ['variant' => 'A'], 'holdout_percent' => 0]);
        $rule->update(['status' => 'active']);
        $decision = app(DecidePersonalization::class)->execute($team->id, $owner->id, ['rule_id' => $rule->id, 'subject_type' => 'customer', 'subject_id' => 12, 'channel' => 'email', 'locale' => 'en-GB', 'attributes' => ['lifecycle' => 'new'], 'consent' => true]);
        app(RecordPersonalizationOutcome::class)->execute($team->id, $owner->id, ['decision_id' => $decision->id, 'event' => 'conversion']);

        self::assertSame('A', $decision->variant);
        self::assertFalse($decision->holdout);
        self::assertCount(1, PersonalizationDecision::query()->where('team_id', $team->id)->get());
        self::assertCount(1, PersonalizationOutcome::query()->where('team_id', $team->id)->get());
        self::assertCount(0, PersonalizationDecision::query()->where('team_id', $other->id)->get());
    }
}
