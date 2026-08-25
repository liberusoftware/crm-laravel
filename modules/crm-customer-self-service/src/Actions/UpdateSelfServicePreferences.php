<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfService\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\CustomerSelfService\Models\SelfServiceProfile;
use Liberu\CRM\CustomerSelfService\Services\SelfServicePolicy;

final class UpdateSelfServicePreferences
{
    public function __construct(private readonly SelfServicePolicy $policy) {}

    public function execute(int $teamId, int $userId, SelfServiceProfile $profile, array $input): SelfServiceProfile
    {
        abort_unless($profile->team_id === $teamId && $profile->user_id === $userId && $this->policy->canAccess($teamId, $userId), 403);
        $data = Validator::make($input, ['preferences' => ['required', 'array']])->validate();
        $profile->update(['preferences' => $data['preferences']]);

        return $profile->refresh();
    }
}
