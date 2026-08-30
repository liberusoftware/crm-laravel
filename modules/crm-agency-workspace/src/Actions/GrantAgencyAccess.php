<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspace\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\AgencyWorkspace\Models\AgencyAccess;
use Liberu\CRM\AgencyWorkspace\Models\AgencyAccount;

final class GrantAgencyAccess
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, int $actorId, AgencyAccount $account, array $input): AgencyAccess
    {
        abort_unless((int) $account->team_id === $teamId, 404);
        $data = Validator::make($input, ['user_id' => ['required', 'integer'], 'role' => ['required', 'in:delegate,support,admin'], 'expires_at' => ['nullable', 'date', 'after:now']])->validate();
        $access = AgencyAccess::query()->updateOrCreate(['account_id' => $account->getKey(), 'user_id' => $data['user_id']], array_merge($data, ['team_id' => $teamId, 'granted_by' => $actorId, 'status' => 'active']));
        $account->audits()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'type' => 'access_granted', 'payload' => ['user_id' => $data['user_id'], 'role' => $data['role']]]);

        return $access;
    }
}
