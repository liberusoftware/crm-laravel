<?php

declare(strict_types=1);

namespace Liberu\CRM\Memberships\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Memberships\Models\MembershipGrant;
use Liberu\CRM\Memberships\Services\MembershipPolicy;

final class ChangeGrantStatus
{
    public function __construct(private readonly MembershipPolicy $policy) {}

    public function execute(int $teamId, int $userId, MembershipGrant $grant, array $input): MembershipGrant
    {
        abort_unless($grant->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['status' => ['required', 'in:active,suspended,expired'], 'renewal_reference' => ['nullable', 'string', 'max:255']])->validate();
        $grant->fill($data)->save();

        return $grant->refresh();
    }
}
