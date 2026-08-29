<?php

declare(strict_types=1);

namespace Tests\Feature\Prospecting;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Prospecting\Actions\CreateIdealCustomerProfile;
use Liberu\CRM\Prospecting\Actions\CreateProspectSearch;
use Liberu\CRM\Prospecting\Actions\ImportProspect;
use Liberu\CRM\Prospecting\Actions\QueueResearch;
use Liberu\CRM\Prospecting\Actions\RevealContact;
use Liberu\CRM\Prospecting\Filament\Resources\ProspectResource;
use Liberu\CRM\Prospecting\Models\Prospect;
use Liberu\CRM\Prospecting\Models\ProspectCredit;
use Tests\TestCase;

final class ProspectingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_prospect_resource_exposes_the_complete_filament_lifecycle(): void
    {
        self::assertSame(['index', 'create', 'edit'], array_keys(ProspectResource::getPages()));
    }

    public function test_provenance_research_and_idempotent_reveal_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $profile = app(CreateIdealCustomerProfile::class)->execute($team->id, $owner->id, ['name' => 'SaaS leaders', 'criteria' => ['industry' => 'software']]);
        $search = app(CreateProspectSearch::class)->execute($team->id, $owner->id, ['profile_id' => $profile->id, 'provider' => 'directory', 'filters' => ['country' => 'GB']]);
        $prospect = app(ImportProspect::class)->execute($team->id, $owner->id, ['search_id' => $search->id, 'provider' => 'directory', 'provider_id' => 'p-1', 'name' => 'Jane Doe', 'company' => 'Acme', 'email' => 'jane@example.com', 'provenance' => ['source' => 'directory']]);
        app(QueueResearch::class)->execute($team->id, $owner->id, ['prospect_id' => $prospect->id, 'priority' => 'high']);
        $credit = app(RevealContact::class)->execute($team->id, $owner->id, ['prospect_id' => $prospect->id, 'kind' => 'email', 'idempotency_key' => 'reveal-1']);
        $same = app(RevealContact::class)->execute($team->id, $owner->id, ['prospect_id' => $prospect->id, 'kind' => 'email', 'idempotency_key' => 'reveal-1']);

        self::assertSame($credit->id, $same->id);
        self::assertSame('directory', $prospect->provenance['source']);
        self::assertCount(1, ProspectCredit::query()->where('team_id', $team->id)->get());
        self::assertCount(0, Prospect::query()->where('team_id', $other->id)->get());
    }
}
