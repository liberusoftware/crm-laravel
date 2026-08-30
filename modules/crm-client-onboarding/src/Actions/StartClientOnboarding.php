<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboarding\Actions;

use Liberu\CRM\ClientOnboarding\Models\ClientOnboarding;

final class StartClientOnboarding
{
    /** @param array<string,mixed> $intake */
    public function execute(int $teamId, int $ownerId, string $clientKey, array $intake = []): ClientOnboarding
    {
        $clientKey = trim($clientKey);
        abort_unless($clientKey !== '' && $intake !== [], 422);

        return ClientOnboarding::query()->updateOrCreate(['team_id' => $teamId, 'client_key' => $clientKey], ['owner_id' => $ownerId, 'status' => 'intake', 'intake' => $intake, 'health' => 0]);
    }
}
