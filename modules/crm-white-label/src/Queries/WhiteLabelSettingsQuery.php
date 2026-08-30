<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Queries;

use Liberu\CRM\WhiteLabel\Models\WhiteLabelSettings;

final class WhiteLabelSettingsQuery
{
    public function forTeam(int $teamId): WhiteLabelSettings
    {
        return WhiteLabelSettings::query()->firstOrCreate(['team_id' => $teamId], ['theme' => 'default', 'provider' => 'platform']);
    }
}
