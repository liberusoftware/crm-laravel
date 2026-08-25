<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\RevenueIntelligence\Models\RevenueIntelligenceAlert;
use Liberu\CRM\RevenueIntelligence\Services\RevenueIntelligencePolicy;

final class CreateAlert
{
    public function __construct(private readonly RevenueIntelligencePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): RevenueIntelligenceAlert
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'string', 'max:100'], 'severity' => ['required', 'in:info,warning,critical'], 'message' => ['required', 'string', 'max:2000'], 'payload' => ['nullable', 'array']])->validate();

        return RevenueIntelligenceAlert::query()->create(['team_id' => $teamId, ...$data]);
    }
}
