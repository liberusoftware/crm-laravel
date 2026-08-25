<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ReputationManagement\Events\ReputationReviewReceived;
use Liberu\CRM\ReputationManagement\Models\ReputationReview;
use Liberu\CRM\ReputationManagement\Services\ReputationPolicy;

final class RecordReview
{
    public function __construct(private readonly ReputationPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ReputationReview
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['connection_id' => ['nullable', 'integer'], 'customer_id' => ['nullable', 'integer'], 'external_id' => ['nullable', 'string', 'max:255'], 'rating' => ['required', 'integer', 'between:1,5'], 'content' => ['nullable', 'string'], 'sentiment' => ['required', 'in:positive,neutral,negative']])->validate();
        $review = ReputationReview::query()->updateOrCreate(['team_id' => $teamId, 'external_id' => $data['external_id'] ?? null], ['team_id' => $teamId, ...$data, 'reviewed_at' => now()]);
        event(new ReputationReviewReceived($review));

        return $review;
    }
}
