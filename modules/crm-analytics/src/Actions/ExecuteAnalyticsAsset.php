<?php

declare(strict_types=1);

namespace Liberu\CRM\Analytics\Actions;

use Liberu\CRM\Analytics\Models\AnalyticsAsset;
use Liberu\CRM\Analytics\Models\AnalyticsExecution;

final class ExecuteAnalyticsAsset
{
    public function execute(int $teamId, int $userId, AnalyticsAsset $asset, array $parameters = [], string $kind = 'preview'): AnalyticsExecution
    {
        abort_unless($asset->team_id === $teamId && $asset->status !== 'archived', 403);

        return AnalyticsExecution::query()->create(['team_id' => $teamId, 'asset_id' => $asset->id, 'actor_id' => $userId, 'kind' => $kind, 'status' => 'completed', 'parameters' => $parameters, 'result' => ['asset_id' => $asset->id, 'version' => $asset->version, 'lineage' => $asset->lineage]]);
    }
}
