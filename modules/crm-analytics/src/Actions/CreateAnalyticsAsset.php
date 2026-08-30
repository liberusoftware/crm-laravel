<?php

declare(strict_types=1);

namespace Liberu\CRM\Analytics\Actions;

use Liberu\CRM\Analytics\Models\AnalyticsAsset;

final class CreateAnalyticsAsset
{
    /** @param array{name?:string,kind?:string,definition?:array<string,mixed>,lineage?:array<string,mixed>} $input */
    public function execute(int $teamId, int $userId, array $input): AnalyticsAsset
    {
        $name = trim((string) ($input['name'] ?? ''));
        $kind = (string) ($input['kind'] ?? '');
        abort_unless($name !== '' && in_array($kind, ['dataset', 'dashboard', 'report', 'pivot', 'funnel', 'cohort', 'goal', 'schedule', 'export'], true), 422);

        return AnalyticsAsset::query()->create(['team_id' => $teamId, 'owner_id' => $userId, 'name' => $name, 'kind' => $kind, 'definition' => $input['definition'] ?? [], 'lineage' => $input['lineage'] ?? []]);
    }
}
