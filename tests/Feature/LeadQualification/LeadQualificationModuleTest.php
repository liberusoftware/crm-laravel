<?php

declare(strict_types=1);

namespace Tests\Feature\LeadQualification;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\LeadQualification\Actions\RecordQualificationEvent;
use Liberu\CRM\LeadQualification\Actions\ScoreLead;
use Liberu\CRM\LeadQualification\Actions\UpsertLead;
use Tests\TestCase;

final class LeadQualificationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_scoring_nurture_and_conversion_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $lead = app(UpsertLead::class)->execute($team->id, $owner->id, ['external_key' => 'lead-1']);
        app(ScoreLead::class)->execute($team->id, $owner->id, $lead, ['fit_score' => 90, 'engagement_score' => 80, 'qualification' => 'SQL']);
        app(RecordQualificationEvent::class)->execute($team->id, $owner->id, $lead, ['kind' => 'conversion', 'to_value' => 'deal-1', 'reason' => 'Accepted']);
        $this->assertDatabaseHas('crm_lead_qualification_leads', ['team_id' => $team->id, 'qualification' => 'SQL', 'stage' => 'converted', 'conversion_reference' => 'deal-1']);
        $this->assertDatabaseHas('crm_lead_qualification_events', ['team_id' => $team->id, 'kind' => 'conversion']);
        $this->assertDatabaseMissing('crm_lead_qualification_leads', ['team_id' => $other->id, 'external_key' => 'lead-1']);
    }
}
