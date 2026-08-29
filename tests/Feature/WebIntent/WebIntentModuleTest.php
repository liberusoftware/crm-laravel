<?php

declare(strict_types=1);

namespace Tests\Feature\WebIntent;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\WebIntent\Actions\ConvertIntent;
use Liberu\CRM\WebIntent\Actions\CreateAlert;
use Liberu\CRM\WebIntent\Actions\IdentifyAccount;
use Liberu\CRM\WebIntent\Actions\RecordEngagement;
use Liberu\CRM\WebIntent\Actions\RecordVisit;
use Liberu\CRM\WebIntent\Actions\ResolveAlert;
use Liberu\CRM\WebIntent\Actions\SetConsent;
use Liberu\CRM\WebIntent\Queries\WebIntentQuery;
use Liberu\CRM\WebIntent\Services\WebIntentPolicy;
use Liberu\CRM\WebIntent\Services\WebIntentScorer;
use Tests\TestCase;

final class WebIntentModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_visit_engagement_scoring_consent_and_summary_are_team_scoped(): void
    {
        $visit = app(RecordVisit::class)->execute(7, 'visitor-a', ['consent_status' => 'granted', 'landing_url' => 'https://example.test/']);
        $engagement = app(RecordEngagement::class)->execute($visit, ['event_type' => 'pricing_view', 'page_url' => 'https://example.test/pricing', 'dedupe_key' => 'event-a'], app(WebIntentScorer::class));
        app(SetConsent::class)->execute(7, 'visitor-a', 'analytics', 'granted', '2026-01');
        app(RecordVisit::class)->execute(8, 'visitor-b', ['consent_status' => 'granted']);

        self::assertSame(8, $engagement->points);
        self::assertSame('cool', $visit->fresh()->intent_level);
        self::assertSame(['total' => 1, 'hot' => 0, 'warm' => 0, 'open_alerts' => 0], app(WebIntentQuery::class)->summary(7));
        self::assertDatabaseHas('crm_web_intent_consents', ['team_id' => 7, 'visitor_key' => 'visitor-a', 'status' => 'granted']);
        self::assertDatabaseHas('crm_web_intent_audits', ['team_id' => 7, 'event' => 'visit_recorded']);
    }

    public function test_denied_consent_is_rejected_and_engagement_deduplicates(): void
    {
        try {
            app(RecordVisit::class)->execute(7, 'visitor-a', ['consent_status' => 'denied']);
            self::fail('Denied consent must not create a visit.');
        } catch (ValidationException) {
            self::assertTrue(true);
        }

        $visit = app(RecordVisit::class)->execute(7, 'visitor-a', ['consent_status' => 'granted']);
        $action = app(RecordEngagement::class);
        $first = $action->execute($visit, ['event_type' => 'page_view', 'dedupe_key' => 'same-event'], app(WebIntentScorer::class));
        $second = $action->execute($visit, ['event_type' => 'page_view', 'dedupe_key' => 'same-event'], app(WebIntentScorer::class));
        self::assertSame($first->id, $second->id);
    }

    public function test_manager_operations_are_authorized_and_idempotent(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $visit = app(RecordVisit::class)->execute($team->id, 'visitor-a', ['consent_status' => 'granted']);
        $policy = app(WebIntentPolicy::class);
        $identification = app(IdentifyAccount::class)->execute($team->id, $owner->id, 'visitor-a', 'domain', 'Acme', 'acme.test', 90, [], $policy);
        $alert = app(CreateAlert::class)->execute($team->id, $owner->id, 'visitor-a', 'Hot visitor', 'high', $visit->id, null, $policy);
        app(ResolveAlert::class)->execute($alert, $owner->id, $policy);
        $first = app(ConvertIntent::class)->execute($team->id, $owner->id, 'visitor-a', 'lead', 42, $visit->id, [], $policy);
        $second = app(ConvertIntent::class)->execute($team->id, $owner->id, 'visitor-a', 'lead', 42, $visit->id, [], $policy);

        self::assertSame('identified', $identification->status);
        self::assertSame($first->id, $second->id);
        self::assertSame('resolved', $alert->fresh()->status);
    }

    public function test_mutations_reject_foreign_visits_and_ignore_control_fields(): void
    {
        $visit = app(RecordVisit::class)->execute(7, 'visitor-a', ['consent_status' => 'granted', 'team_id' => 99, 'status' => 'ended']);
        $foreignVisit = app(RecordVisit::class)->execute(8, 'visitor-b', ['consent_status' => 'granted']);
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $policy = app(WebIntentPolicy::class);

        self::assertSame(7, $visit->team_id);
        self::assertSame('active', $visit->status);

        $this->expectException(ValidationException::class);
        app(CreateAlert::class)->execute($team->id, $owner->id, 'visitor-b', 'Foreign visit', 'high', $foreignVisit->id, null, $policy);
    }

    public function test_conversion_rejects_a_foreign_visit(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $foreignVisit = app(RecordVisit::class)->execute(8, 'visitor-b', ['consent_status' => 'granted']);

        $this->expectException(ValidationException::class);
        app(ConvertIntent::class)->execute($team->id, $owner->id, 'visitor-b', 'lead', 42, $foreignVisit->id, [], app(WebIntentPolicy::class));
    }
}
