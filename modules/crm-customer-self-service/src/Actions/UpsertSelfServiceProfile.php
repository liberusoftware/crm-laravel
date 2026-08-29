<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfService\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\CustomerSelfService\Models\SelfServiceProfile;
use Liberu\CRM\CustomerSelfService\Services\SelfServicePolicy;

final class UpsertSelfServiceProfile
{
    public function __construct(private readonly SelfServicePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): SelfServiceProfile
    {
        abort_unless($this->policy->canAccess($teamId, $userId), 403);
        $data = Validator::make($input, ['display_name' => ['required', 'string', 'max:160'], 'email' => ['required', 'email'], 'preferences' => ['nullable', 'array']])->validate();

        return SelfServiceProfile::query()->updateOrCreate(['team_id' => $teamId, 'user_id' => $userId], ['team_id' => $teamId, ...$data]);
    }
}
