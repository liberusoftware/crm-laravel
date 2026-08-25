<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Queries;

use Liberu\CRM\Personalization\Models\PersonalizationDecision;
use Liberu\CRM\Personalization\Models\PersonalizationOutcome;
use Liberu\CRM\Personalization\Models\PersonalizationRule;

final class PersonalizationQuery
{
    public function rules(int $teamId)
    {
        return PersonalizationRule::query()->where('team_id', $teamId)->latest();
    }

    public function decisions(int $teamId)
    {
        return PersonalizationDecision::query()->where('team_id', $teamId)->latest();
    }

    public function outcomes(int $teamId)
    {
        return PersonalizationOutcome::query()->where('team_id', $teamId)->latest();
    }
}
