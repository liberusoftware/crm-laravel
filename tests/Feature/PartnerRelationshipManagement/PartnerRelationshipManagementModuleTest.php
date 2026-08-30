<?php

declare(strict_types=1);

namespace Tests\Feature\PartnerRelationshipManagement;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\PartnerRelationshipManagement\Actions\ChangePartnerStatus;
use Liberu\CRM\PartnerRelationshipManagement\Actions\CreatePartner;
use Liberu\CRM\PartnerRelationshipManagement\Actions\RecordPartnerActivity;
use Liberu\CRM\PartnerRelationshipManagement\Actions\RecordPartnerPerformance;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerAccount;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerPerformance;
use Tests\TestCase;

final class PartnerRelationshipManagementModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_onboarding_activity_and_performance_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $partner = app(CreatePartner::class)->execute($team->id, $owner->id, ['name' => 'Acme Partners', 'tier' => 'select', 'competencies' => ['implementation']]);
        app(ChangePartnerStatus::class)->execute($team->id, $owner->id, $partner->id, 'onboarding');
        app(RecordPartnerActivity::class)->execute($team->id, $owner->id, ['partner_id' => $partner->id, 'kind' => 'enablement', 'payload' => ['course' => 'CRM 101']]);
        app(RecordPartnerPerformance::class)->execute($team->id, $owner->id, ['partner_id' => $partner->id, 'period_start' => '2026-08-01', 'period_end' => '2026-08-31', 'revenue' => 10000, 'deals' => 2, 'score' => 85]);

        self::assertSame('onboarding', $partner->refresh()->status);
        self::assertCount(1, PartnerPerformance::query()->where('team_id', $team->id)->get());
        self::assertCount(0, PartnerAccount::query()->where('team_id', $other->id)->get());
    }
}
