<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Events;

use Liberu\CRM\ReputationManagement\Models\ReputationReview;

final readonly class ReputationReviewReceived
{
    public function __construct(public ReputationReview $review) {}
}
