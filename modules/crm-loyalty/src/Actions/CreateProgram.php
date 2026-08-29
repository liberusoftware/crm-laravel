<?php

declare(strict_types=1);

namespace Liberu\CRM\Loyalty\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Loyalty\Models\LoyaltyProgram;
use Liberu\CRM\Loyalty\Services\LoyaltyPolicy;

final class CreateProgram
{
    public function __construct(private readonly LoyaltyPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): LoyaltyProgram
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'status' => ['nullable', 'in:draft,active,archived'], 'currency' => ['required', 'string', 'size:3'], 'tiers' => ['nullable', 'array'], 'metadata' => ['nullable', 'array']])->validate();

        return LoyaltyProgram::query()->create(['team_id' => $teamId, ...$data]);
    }
}
