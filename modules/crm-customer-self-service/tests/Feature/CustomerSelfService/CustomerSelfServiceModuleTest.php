<?php

declare(strict_types=1);

namespace Tests\Feature\CustomerSelfService;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\CustomerSelfService\Actions\SubmitSelfServiceCase;
use Liberu\CRM\CustomerSelfService\Actions\UpdateSelfServicePreferences;
use Liberu\CRM\CustomerSelfService\Actions\UpsertSelfServiceProfile;
use Tests\TestCase;

final class CustomerSelfServiceModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_preferences_and_case_submission_are_user_and_team_scoped(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $profile = app(UpsertSelfServiceProfile::class)->execute($team->id, $user->id, ['display_name' => 'Ada', 'email' => 'ada@example.test']);
        $updated = app(UpdateSelfServicePreferences::class)->execute($team->id, $user->id, $profile, ['preferences' => ['email_updates' => true]]);
        $case = app(SubmitSelfServiceCase::class)->execute($team->id, $user->id, $updated, ['subject' => 'Need help', 'description' => 'Please assist', 'priority' => 'high']);
        $this->assertTrue((bool) $updated->preferences['email_updates']);
        $this->assertSame('open', $case->status);
        $this->assertDatabaseHas('crm_self_service_cases', ['team_id' => $team->id, 'profile_id' => $profile->id]);
    }
}
