<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspace\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\AgencyWorkspace\Models\AgencyAccount;

final class UpdateAgencyUsage
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, int $actorId, AgencyAccount $account, array $input): AgencyAccount
    {
        abort_unless((int) $account->team_id === $teamId, 404);
        $usage = Validator::make($input, ['usage' => ['required', 'array']])->validate()['usage'];
        $account->update(['usage_snapshot' => $usage]);
        $account->audits()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'type' => 'usage_updated', 'payload' => $usage]);

        return $account->fresh();
    }
}
