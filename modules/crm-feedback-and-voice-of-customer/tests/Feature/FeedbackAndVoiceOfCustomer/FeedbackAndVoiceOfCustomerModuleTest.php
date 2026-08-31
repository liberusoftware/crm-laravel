<?php

declare(strict_types=1);

namespace Tests\Feature\FeedbackAndVoiceOfCustomer;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions\CreateFeedbackSurvey;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions\DeliverFeedbackSurvey;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions\OpenFeedbackCase;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions\RecordFeedbackResponse;
use Tests\TestCase;

final class FeedbackAndVoiceOfCustomerModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_feedback_delivery_response_and_close_loop_case_are_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $survey = app(CreateFeedbackSurvey::class)->execute($team->id, $owner->id, ['name' => 'Customer pulse', 'slug' => 'customer-pulse', 'metric' => 'nps', 'status' => 'published', 'questions' => [['key' => 'score']]]);
        $delivery = app(DeliverFeedbackSurvey::class)->execute($team->id, $owner->id, $survey, $owner->id);
        $response = app(RecordFeedbackResponse::class)->execute($team->id, $delivery, ['score' => 9, 'comment' => 'Excellent', 'sentiment' => 'positive']);
        $case = app(OpenFeedbackCase::class)->execute($team->id, $owner->id, $response, $owner->id);
        $this->assertSame($team->id, $case->team_id);
        $this->assertSame('responded', $delivery->fresh()->status);
        $this->assertDatabaseHas('crm_feedback_responses', ['survey_id' => $survey->id, 'score' => 9]);
    }
}
