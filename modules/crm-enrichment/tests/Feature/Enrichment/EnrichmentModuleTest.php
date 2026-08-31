<?php

declare(strict_types=1);

namespace Tests\Feature\Enrichment;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Enrichment\Actions\RecordChange;
use Liberu\CRM\Enrichment\Actions\RecordProvenance;
use Liberu\CRM\Enrichment\Actions\UpsertEnrichment;
use Liberu\CRM\Enrichment\Actions\VerifyProfile;
use Tests\TestCase;

final class EnrichmentModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrichment_confidence_provenance_verification_and_changes_are_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $profile = app(UpsertEnrichment::class)->execute($team->id, $owner->id, ['entity_type' => 'company', 'entity_key' => 'example.com', 'confidence' => 85, 'firmographic' => ['industry' => 'software'], 'provider' => 'directory']);
        $field = app(RecordProvenance::class)->execute($team->id, $owner->id, $profile, ['field' => 'industry', 'value' => 'software', 'source' => 'directory', 'confidence' => 90]);
        $change = app(RecordChange::class)->execute($team->id, $owner->id, $profile, ['field' => 'industry', 'before' => 'software', 'after' => 'platform', 'status' => 'detected']);
        $verified = app(VerifyProfile::class)->execute($team->id, $owner->id, $profile, true);
        $this->assertSame(90, $field->confidence);
        $this->assertSame('detected', $change->status);
        $this->assertTrue((bool) $verified->verification['verified']);
        $this->assertDatabaseHas('crm_enrichment_profiles', ['team_id' => $team->id, 'entity_key' => 'example.com']);
    }
}
