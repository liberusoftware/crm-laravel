<?php

declare(strict_types=1);

namespace Liberu\CRM\ContactCenter\Actions;

use Liberu\CRM\ContactCenter\Models\ContactCenterEvent;

final class ReviewQuality
{
    public function execute(int $teamId, int $eventId, int $reviewerId, array $scorecard): ContactCenterEvent
    {
        $event = ContactCenterEvent::query()->where('team_id', $teamId)->findOrFail($eventId);
        abort_unless($scorecard !== [], 422);
        $event->update(['type' => 'quality_reviewed', 'payload' => ['reviewer_id' => $reviewerId, 'scorecard' => $scorecard]]);

        return $event->refresh();
    }
}
