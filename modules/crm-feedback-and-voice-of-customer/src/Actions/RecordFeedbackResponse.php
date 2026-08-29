<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackDelivery;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackResponse;

final class RecordFeedbackResponse
{
    public function execute(int $teamId, FeedbackDelivery $delivery, array $input): FeedbackResponse
    {
        abort_unless($delivery->team_id === $teamId, 403);
        $data = Validator::make($input, ['score' => ['required', 'integer', 'between:0,10'], 'comment' => ['nullable', 'string', 'max:10000'], 'sentiment' => ['nullable', 'in:positive,neutral,negative'], 'answers' => ['nullable', 'array'], 'respondent_id' => ['nullable', 'integer']])->validate();
        $response = FeedbackResponse::query()->create(['team_id' => $teamId, 'survey_id' => $delivery->survey_id, 'delivery_id' => $delivery->id, ...$data]);
        $delivery->update(['status' => 'responded', 'responded_at' => now()]);

        return $response;
    }
}
