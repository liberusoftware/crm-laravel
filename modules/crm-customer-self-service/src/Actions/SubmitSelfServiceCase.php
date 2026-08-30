<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfService\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\CustomerSelfService\Models\SelfServiceCase;
use Liberu\CRM\CustomerSelfService\Models\SelfServiceProfile;
use Liberu\CRM\CustomerSelfService\Services\SelfServicePolicy;

final class SubmitSelfServiceCase
{
    public function __construct(private readonly SelfServicePolicy $policy) {}

    public function execute(int $teamId, int $userId, SelfServiceProfile $profile, array $input): SelfServiceCase
    {
        abort_unless($profile->team_id === $teamId && $profile->user_id === $userId && $this->policy->canAccess($teamId, $userId), 403);
        $data = Validator::make($input, ['subject' => ['required', 'string', 'max:180'], 'description' => ['required', 'string'], 'priority' => ['nullable', 'in:low,normal,high,urgent']])->validate();

        return SelfServiceCase::query()->create(['team_id' => $teamId, 'profile_id' => $profile->id, 'status' => 'open', ...$data]);
    }
}
