<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Queries;

use Liberu\CRM\ReputationManagement\Models\ReputationConnection;
use Liberu\CRM\ReputationManagement\Models\ReputationRequest;
use Liberu\CRM\ReputationManagement\Models\ReputationReview;
use Liberu\CRM\ReputationManagement\Models\ReputationRollup;
use Liberu\CRM\ReputationManagement\Models\ReputationTemplate;

final class ReputationQuery
{
    public function connections(int $teamId)
    {
        return ReputationConnection::query()->where('team_id', $teamId)->latest();
    }

    public function requests(int $teamId)
    {
        return ReputationRequest::query()->where('team_id', $teamId)->latest();
    }

    public function reviews(int $teamId)
    {
        return ReputationReview::query()->where('team_id', $teamId)->latest();
    }

    public function templates(int $teamId)
    {
        return ReputationTemplate::query()->where('team_id', $teamId)->latest();
    }

    public function rollups(int $teamId)
    {
        return ReputationRollup::query()->where('team_id', $teamId)->latest();
    }
}
