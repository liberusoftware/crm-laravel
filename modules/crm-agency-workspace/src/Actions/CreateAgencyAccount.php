<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspace\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\AgencyWorkspace\Models\AgencyAccount;

final class CreateAgencyAccount
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, int $ownerId, array $input): AgencyAccount
    {
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:180'], 'account_type' => ['nullable', 'in:agency,client,sub_account'], 'parent_id' => ['nullable', 'integer'], 'branding' => ['nullable', 'array']])->validate();
        if (isset($data['parent_id'])) {
            abort_unless(AgencyAccount::query()->where('team_id', $teamId)->whereKey($data['parent_id'])->exists(), 422);
        }

        return AgencyAccount::query()->create(array_merge($data, ['team_id' => $teamId, 'owner_id' => $ownerId, 'account_type' => $data['account_type'] ?? 'client', 'status' => 'active', 'usage_snapshot' => []]));
    }
}
