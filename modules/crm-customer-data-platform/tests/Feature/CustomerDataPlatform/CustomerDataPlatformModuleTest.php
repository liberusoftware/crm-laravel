<?php

declare(strict_types=1);

namespace Tests\Feature\CustomerDataPlatform;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\CustomerDataPlatform\Actions\ActivateCdpAudience;
use Liberu\CRM\CustomerDataPlatform\Actions\CreateCdpAudience;
use Liberu\CRM\CustomerDataPlatform\Actions\IngestCdpEvent;
use Liberu\CRM\CustomerDataPlatform\Actions\UpsertCdpProfile;
use Tests\TestCase;

final class CustomerDataPlatformModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_consent_aware_profile_event_and_audience_activation_are_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $profile = app(UpsertCdpProfile::class)->execute($team->id, $owner->id, ['profile_key' => 'contact-1', 'attributes' => ['name' => 'Ada'], 'consent' => ['analytics' => true]]);
        $event = app(IngestCdpEvent::class)->execute($team->id, $profile, ['name' => 'login', 'payload' => ['source' => 'web'], 'consented' => true]);
        $audience = app(CreateCdpAudience::class)->execute($team->id, $owner->id, ['name' => 'Active', 'definition' => ['event' => 'login']]);
        $activated = app(ActivateCdpAudience::class)->execute($team->id, $owner->id, $audience, 'email');
        $this->assertSame($team->id, $event->team_id);
        $this->assertSame('active', $activated->status);
        $this->assertSame(['email'], $activated->activations);
    }
}
