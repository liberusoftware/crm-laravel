<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Prospecting\Models\IdealCustomerProfile;
use Liberu\CRM\Prospecting\Services\ProspectingPolicy;

final class CreateIdealCustomerProfile
{
    public function __construct(private readonly ProspectingPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): IdealCustomerProfile
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'criteria' => ['required', 'array']])->validate();

        return IdealCustomerProfile::query()->create(['team_id' => $teamId, ...$data]);
    }
}
