<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerAccount;
use Liberu\CRM\PartnerRelationshipManagement\Services\PartnerPolicy;

final class CreatePartner
{
    public function __construct(private readonly PartnerPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PartnerAccount
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'tier' => ['required', 'in:registered,select,strategic'], 'competencies' => ['nullable', 'array'], 'metadata' => ['nullable', 'array']])->validate();

        return PartnerAccount::query()->create(['team_id' => $teamId, ...$data]);
    }
}
