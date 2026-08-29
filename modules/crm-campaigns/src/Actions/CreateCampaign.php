<?php

declare(strict_types=1);

namespace Liberu\CRM\Campaigns\Actions;

use Liberu\CRM\Campaigns\Models\Campaign;

final class CreateCampaign
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, int $ownerId, array $input): Campaign
    {
        $name = trim((string) ($input['name'] ?? ''));
        abort_unless($name !== '' && ($input['objectives'] ?? []) !== [], 422);

        return Campaign::query()->create(['team_id' => $teamId, 'owner_id' => $ownerId, 'parent_id' => $input['parent_id'] ?? null, 'name' => $name, 'brief' => $input['brief'] ?? null, 'objectives' => $input['objectives'], 'audience' => $input['audience'] ?? [], 'channels' => $input['channels'] ?? [], 'assets' => $input['assets'] ?? [], 'budget' => $input['budget'] ?? 0, 'starts_on' => $input['starts_on'] ?? null, 'ends_on' => $input['ends_on'] ?? null]);
    }
}
