<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ReputationManagement\Models\ReputationReview;
use Liberu\CRM\ReputationManagement\Services\ReputationPolicy;

final class RespondToReview
{
    public function __construct(private readonly ReputationPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $reviewId, array $input): ReputationReview
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['response' => ['required', 'string', 'max:5000']])->validate();
        $review = ReputationReview::query()->where('team_id', $teamId)->findOrFail($reviewId);
        $review->update(['response' => $data['response'], 'status' => 'responded']);

        return $review->refresh();
    }
}
