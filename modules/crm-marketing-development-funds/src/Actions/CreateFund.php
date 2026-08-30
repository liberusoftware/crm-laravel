<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFunds\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\MarketingDevelopmentFunds\Models\MdfFund;
use Liberu\CRM\MarketingDevelopmentFunds\Services\MdfPolicy;

final class CreateFund
{
    public function __construct(private readonly MdfPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): MdfFund
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'status' => ['nullable', 'in:draft,active,closed'], 'budget' => ['required', 'numeric', 'min:0'], 'currency' => ['required', 'string', 'size:3'], 'starts_on' => ['required', 'date'], 'ends_on' => ['nullable', 'date'], 'metadata' => ['nullable', 'array']])->validate();

        return MdfFund::query()->create(['team_id' => $teamId, ...$data]);
    }
}
