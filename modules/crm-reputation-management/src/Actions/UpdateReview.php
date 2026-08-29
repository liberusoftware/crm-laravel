<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ReputationManagement\Models\ReputationReview;
use Liberu\CRM\ReputationManagement\Services\ReputationPolicy;

final class UpdateReview
{
    public function __construct(private readonly ReputationPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $reviewId, array $input): ReputationReview
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, [
            'connection_id' => ['nullable', 'integer'],
            'customer_id' => ['nullable', 'integer'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'sentiment' => ['required', 'in:positive,neutral,negative'],
            'content' => ['nullable', 'string'],
            'response' => ['nullable', 'string'],
        ])->validate();
        $review = ReputationReview::query()->where('team_id', $teamId)->findOrFail($reviewId);
        $review->update($data);

        return $review;
    }
}
