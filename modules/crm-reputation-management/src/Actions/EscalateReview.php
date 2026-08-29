<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Actions;

use Liberu\CRM\ReputationManagement\Models\ReputationReview;
use Liberu\CRM\ReputationManagement\Services\ReputationPolicy;

final class EscalateReview
{
    public function __construct(private readonly ReputationPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $reviewId): ReputationReview
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $review = ReputationReview::query()->where('team_id', $teamId)->findOrFail($reviewId);
        $review->update(['status' => 'escalated']);

        return $review->refresh();
    }
}
