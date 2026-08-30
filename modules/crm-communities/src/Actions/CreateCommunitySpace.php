<?php

declare(strict_types=1);

namespace Liberu\CRM\Communities\Actions;

use Liberu\CRM\Communities\Models\CommunitySpace;

final class CreateCommunitySpace
{
    public function execute(int $teamId, int $ownerId, string $name, string $kind = 'customer', array $settings = []): CommunitySpace
    {
        $name = trim($name);
        abort_unless($name !== '' && in_array($kind, ['customer', 'partner', 'internal'], true), 422);

        return CommunitySpace::query()->create(['team_id' => $teamId, 'owner_id' => $ownerId, 'name' => $name, 'kind' => $kind, 'status' => 'active', 'settings' => $settings]);
    }
}
