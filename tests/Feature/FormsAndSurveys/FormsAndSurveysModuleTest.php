<?php

declare(strict_types=1);

namespace Tests\Feature\FormsAndSurveys;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\FormsAndSurveys\Actions\CreateSurveyForm;
use Liberu\CRM\FormsAndSurveys\Actions\RecordFollowUp;
use Liberu\CRM\FormsAndSurveys\Actions\SubmitForm;
use Tests\TestCase;

final class FormsAndSurveysModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_consent_spam_attribution_submission_and_follow_up_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $form = app(CreateSurveyForm::class)->execute($team->id, $owner->id, ['slug' => 'contact', 'kind' => 'form', 'status' => 'published', 'schema' => [['name' => 'email', 'required' => true]], 'embedding' => ['allowed_origins' => ['https://example.test']]]);
        $submission = app(SubmitForm::class)->execute($team->id, $owner->id, $form, ['consent' => true, 'spam_status' => 'passed', 'visitor_key' => 'v-1', 'attribution' => ['utm_source' => 'newsletter'], 'payload' => ['email' => 'a@example.test']]);
        app(RecordFollowUp::class)->execute($team->id, $owner->id, $submission, ['kind' => 'notification', 'status' => 'queued', 'details' => 'Notify sales']);
        $this->assertDatabaseHas('crm_forms_submissions', ['team_id' => $team->id, 'consent' => 1, 'spam_status' => 'passed']);
        $this->assertDatabaseHas('crm_forms_follow_ups', ['team_id' => $team->id, 'status' => 'queued']);
        $this->assertDatabaseMissing('crm_forms_surveys', ['team_id' => $other->id, 'slug' => 'contact']);
    }
}
