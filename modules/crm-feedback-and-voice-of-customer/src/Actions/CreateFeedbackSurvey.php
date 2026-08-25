<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackSurvey;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Services\FeedbackPolicy;

final class CreateFeedbackSurvey
{
    public function __construct(private readonly FeedbackPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): FeedbackSurvey
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:160'], 'slug' => ['required', 'alpha_dash', 'max:80'], 'metric' => ['required', 'in:csat,nps,ces,custom'], 'status' => ['nullable', 'in:draft,published,archived'], 'questions' => ['required', 'array'], 'sampling' => ['nullable', 'array'], 'delivery' => ['nullable', 'array']])->validate();

        return FeedbackSurvey::query()->create(['team_id' => $teamId, ...$data]);
    }
}
