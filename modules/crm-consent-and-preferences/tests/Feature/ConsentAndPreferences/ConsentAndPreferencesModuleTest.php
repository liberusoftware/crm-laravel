<?php

declare(strict_types=1);

namespace Tests\Feature\ConsentAndPreferences;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ConsentAndPreferences\Actions\GrantConsent;
use Liberu\CRM\ConsentAndPreferences\Actions\SetPreference;
use Liberu\CRM\ConsentAndPreferences\Actions\SuppressSubject;
use Liberu\CRM\ConsentAndPreferences\Actions\WithdrawConsent;
use Liberu\CRM\ConsentAndPreferences\Models\ConsentRecord;
use Liberu\CRM\ConsentAndPreferences\Services\PolicyEvaluator;
use Tests\TestCase;

final class ConsentAndPreferencesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_grant_preference_and_policy_evaluation_are_team_scoped(): void
    {
        app(GrantConsent::class)->execute(7, 'contact', 42, ['channel' => 'email', 'topic' => 'product', 'lawful_basis' => 'consent', 'source' => 'web-form', 'proof' => ['ip' => '192.0.2.1']]);
        app(SetPreference::class)->execute(7, 'contact', 42, 'email', 'product', ['state' => 'allowed', 'quiet_hours' => ['from' => '22:00', 'to' => '07:00'], 'timezone' => 'UTC']);
        $decision = app(PolicyEvaluator::class)->evaluate(7, 'contact', 42, 'email', 'product');
        self::assertTrue($decision['allowed']);
        self::assertSame([], $decision['reasons']);
        self::assertDatabaseHas('crm_policy_evaluations', ['team_id' => 7, 'allowed' => true]);
    }

    public function test_suppression_and_withdrawal_fail_closed(): void
    {
        $consent = app(GrantConsent::class)->execute(7, 'contact', 42, ['channel' => 'phone', 'topic' => 'recording', 'lawful_basis' => 'consent', 'source' => 'agent', 'proof' => ['recording_id' => 'rec-1']]);
        app(SuppressSubject::class)->execute(7, 'contact', 42, ['channel' => 'phone', 'topic' => 'recording', 'reason' => 'user request', 'source' => 'portal']);
        $suppressed = app(PolicyEvaluator::class)->evaluate(7, 'contact', 42, 'phone', 'recording');
        self::assertFalse($suppressed['allowed']);
        self::assertContains('suppressed', $suppressed['reasons']);
        app(WithdrawConsent::class)->execute($consent);
        self::assertFalse(ConsentRecord::query()->findOrFail($consent->getKey())->isActive());
    }

    public function test_expired_consent_is_not_allowed(): void
    {
        app(GrantConsent::class)->execute(7, 'contact', 42, ['channel' => 'sms', 'topic' => 'offers', 'lawful_basis' => 'consent', 'source' => 'import', 'proof' => ['row' => 5], 'expires_at' => now()->subMinute()]);
        $decision = app(PolicyEvaluator::class)->evaluate(7, 'contact', 42, 'sms', 'offers');
        self::assertFalse($decision['allowed']);
        self::assertContains('missing_or_inactive_consent', $decision['reasons']);
    }
}
