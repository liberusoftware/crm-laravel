<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ResourcePlanning\Models\ResourceRate;
use Liberu\CRM\ResourcePlanning\Services\ResourcePlanningPolicy;

final class SetRate
{
    public function __construct(private readonly ResourcePlanningPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ResourceRate
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['resource_id' => ['nullable', 'integer'], 'skill_id' => ['nullable', 'integer'], 'hourly_rate' => ['required', 'numeric', 'min:0'], 'currency' => ['required', 'string', 'size:3'], 'effective_from' => ['required', 'date'], 'effective_until' => ['nullable', 'date', 'after_or_equal:effective_from']])->validate();

        return ResourceRate::query()->create(['team_id' => $teamId, ...$data]);
    }
}
