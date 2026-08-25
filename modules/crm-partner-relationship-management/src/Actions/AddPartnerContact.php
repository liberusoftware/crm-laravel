<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerAccount;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerContact;
use Liberu\CRM\PartnerRelationshipManagement\Services\PartnerPolicy;

final class AddPartnerContact
{
    public function __construct(private readonly PartnerPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PartnerContact
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['partner_id' => ['required', 'integer'], 'name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email'], 'role' => ['nullable', 'string', 'max:100']])->validate();
        PartnerAccount::query()->where('team_id', $teamId)->findOrFail($data['partner_id']);

        return PartnerContact::query()->create(['team_id' => $teamId, ...$data]);
    }
}
