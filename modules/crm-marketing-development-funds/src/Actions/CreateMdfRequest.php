<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFunds\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\MarketingDevelopmentFunds\Models\MdfFund;
use Liberu\CRM\MarketingDevelopmentFunds\Models\MdfRequest;
use Liberu\CRM\MarketingDevelopmentFunds\Services\MdfPolicy;

final class CreateMdfRequest
{
    public function __construct(private readonly MdfPolicy $policy) {}

    public function execute(int $teamId, int $userId, MdfFund $fund, array $input): MdfRequest
    {
        abort_unless($fund->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['title' => ['required', 'string', 'max:255'], 'partner_id' => ['nullable', 'integer'], 'amount' => ['required', 'numeric', 'min:0.01'], 'metadata' => ['nullable', 'array']])->validate();
        abort_if((float) $data['amount'] > (float) $fund->budget - (float) $fund->committed, 422, 'MDF budget exceeded.');
        $request = MdfRequest::query()->create(['team_id' => $teamId, 'fund_id' => $fund->id, ...$data]);
        $fund->increment('committed', (float) $data['amount']);

        return $request;
    }
}
